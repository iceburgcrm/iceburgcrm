package web

import (
	"context"
	"crypto/hmac"
	"crypto/sha256"
	"database/sql"
	"encoding/base64"
	"encoding/json"
	"fmt"
	"html/template"
	"net/http"
	"os"
	"path/filepath"
	"strconv"
	"strings"
	"time"

	"iceburgcrm/goversion/internal/app"
	"iceburgcrm/goversion/internal/db"
)

type Server struct {
	cfg app.Config
	db  *sql.DB
	tpl *template.Template
}

type ctxKey string

const userKey ctxKey = "user"

func NewServer(cfg app.Config, database *sql.DB) *Server {
	return &Server{cfg: cfg, db: database, tpl: template.Must(template.New("app").Parse(appHTML))}
}

func (s *Server) Routes() http.Handler {
	mux := http.NewServeMux()
	mux.Handle("/css/", http.FileServer(http.Dir(s.cfg.PublicFS)))
	mux.Handle("/js/", http.FileServer(http.Dir(s.cfg.PublicFS)))
	mux.Handle("/images/", http.FileServer(http.Dir(s.cfg.PublicFS)))
	mux.HandleFunc("/favicon.ico", s.staticFile("favicon.ico"))
	mux.HandleFunc("/mix-manifest.json", s.staticFile("mix-manifest.json"))
	mux.HandleFunc("/robots.txt", s.staticFile("robots.txt"))
	mux.HandleFunc("/lang/", s.lang)
	mux.HandleFunc("/login", s.login)
	mux.HandleFunc("/register", s.register)
	mux.HandleFunc("/logout", s.logout)
	mux.HandleFunc("/forgot-password", s.forgotPassword)
	mux.HandleFunc("/reset-password", s.resetPassword)
	mux.HandleFunc("/reset-password/", s.resetPassword)
	mux.HandleFunc("/confirm-password", s.auth(s.confirmPassword))
	mux.HandleFunc("/email/verification-notification", s.auth(s.verificationNotification))
	mux.HandleFunc("/dashboard", s.auth(s.dashboard))
	mux.HandleFunc("/calendar", s.auth(s.calendar))
	mux.HandleFunc("/modules", s.auth(s.modulesPage))
	mux.HandleFunc("/settings", s.auth(s.settings))
	mux.HandleFunc("/role_permission", s.auth(s.rolePermission))
	mux.HandleFunc("/import", s.auth(s.importPage))
	mux.HandleFunc("/admin/", s.auth(s.adminOnly(s.admin)))
	mux.HandleFunc("/admin", s.auth(s.adminOnly(s.adminIndex)))
	mux.HandleFunc("/data/", s.auth(s.data))
	mux.HandleFunc("/data", s.auth(s.data))
	mux.HandleFunc("/admin_data/", s.auth(s.adminOnly(s.adminData)))
	mux.HandleFunc("/admin_data", s.auth(s.adminOnly(s.adminData)))
	mux.HandleFunc("/api/login", s.apiLogin)
	mux.HandleFunc("/api/user", s.auth(s.apiUser))
	mux.HandleFunc("/api/crm", s.auth(s.apiCRM))
	mux.HandleFunc("/api/crm/", s.auth(s.apiCRM))
	mux.HandleFunc("/audit_log/", s.auth(s.auditLog))
	mux.HandleFunc("/subpanel/", s.auth(s.subpanelPage))
	mux.HandleFunc("/relationship/", s.auth(s.relationshipPage))
	mux.HandleFunc("/module/", s.auth(s.modulePage))
	mux.HandleFunc("/", s.root)
	return s.recover(mux)
}

func (s *Server) root(w http.ResponseWriter, r *http.Request) {
	if r.URL.Path != "/" {
		http.NotFound(w, r)
		return
	}
	if s.currentUser(r) != nil {
		http.Redirect(w, r, "/dashboard", http.StatusFound)
		return
	}
	s.render(w, r, "Auth/Login", map[string]any{"canResetPassword": true, "canRegister": false, "status": ""})
}

func (s *Server) staticFile(name string) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		http.ServeFile(w, r, filepath.Join(s.cfg.PublicFS, name))
	}
}

func (s *Server) resourceFile(parts ...string) string {
	candidates := []string{
		filepath.Join(append([]string{"resources"}, parts...)...),
		filepath.Join(append([]string{"..", "resources"}, parts...)...),
	}
	for _, candidate := range candidates {
		if _, err := os.Stat(candidate); err == nil {
			return candidate
		}
	}
	return candidates[0]
}

func (s *Server) recover(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("X-Content-Type-Options", "nosniff")
		w.Header().Set("X-Frame-Options", "SAMEORIGIN")
		w.Header().Set("Referrer-Policy", "strict-origin-when-cross-origin")
		if r.Method == http.MethodOptions {
			w.Header().Set("Allow", "GET,POST,PUT,PATCH,DELETE,OPTIONS")
			w.WriteHeader(http.StatusNoContent)
			return
		}
		defer func() {
			if err := recover(); err != nil {
				http.Error(w, fmt.Sprint(err), http.StatusInternalServerError)
			}
		}()
		next.ServeHTTP(w, r)
	})
}

func (s *Server) auth(next http.HandlerFunc) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		user := s.currentUser(r)
		if user == nil {
			if wantsJSON(r) {
				writeJSON(w, http.StatusUnauthorized, map[string]any{"error": "Unauthenticated"})
				return
			}
			http.Redirect(w, r, "/login", http.StatusFound)
			return
		}
		next(w, r.WithContext(context.WithValue(r.Context(), userKey, user)))
	}
}

func (s *Server) adminOnly(next http.HandlerFunc) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		if !s.isAdmin(r) {
			if wantsJSON(r) {
				writeJSON(w, http.StatusForbidden, map[string]any{"error": "No Access"})
				return
			}
			http.Redirect(w, r, "/dashboard", http.StatusFound)
			return
		}
		next(w, r)
	}
}

func (s *Server) isAdmin(r *http.Request) bool {
	user := s.currentUser(r)
	return user != nil && user.Role == "Admin"
}

func (s *Server) currentUser(r *http.Request) *db.User {
	if user, ok := r.Context().Value(userKey).(*db.User); ok {
		return user
	}
	if auth := r.Header.Get("Authorization"); strings.HasPrefix(auth, "Bearer ") {
		token := strings.TrimPrefix(auth, "Bearer ")
		parts := strings.Split(token, ".")
		if len(parts) == 2 && s.verify(parts[0], parts[1]) {
			id, _ := strconv.ParseInt(parts[0], 10, 64)
			user, _ := s.userByID(r.Context(), id)
			return user
		}
	}
	cookie, err := r.Cookie("iceburg_go_session")
	if err != nil {
		return nil
	}
	parts := strings.Split(cookie.Value, ".")
	if len(parts) != 2 || !s.verify(parts[0], parts[1]) {
		return nil
	}
	id, _ := strconv.ParseInt(parts[0], 10, 64)
	user, _ := s.userByID(r.Context(), id)
	return user
}

func (s *Server) sign(value string) string {
	mac := hmac.New(sha256.New, []byte(s.cfg.AppKey))
	mac.Write([]byte(value))
	return base64.RawURLEncoding.EncodeToString(mac.Sum(nil))
}

func (s *Server) verify(value, sig string) bool {
	expected := s.sign(value)
	return hmac.Equal([]byte(expected), []byte(sig))
}

func (s *Server) userByID(ctx context.Context, id int64) (*db.User, error) {
	rows, err := s.db.QueryContext(ctx, `SELECT u.id,u.name,u.email,u.password,u.role_id,COALESCE(r.name,''),COALESCE(u.profile_pic,''),u.ice_slug,COALESCE(CAST(u.created_at AS CHAR),''),COALESCE(CAST(u.updated_at AS CHAR),'') FROM ice_users u LEFT JOIN ice_roles r ON r.id=u.role_id WHERE u.id=? LIMIT 1`, id)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	if !rows.Next() {
		return nil, nil
	}
	user := &db.User{}
	return user, rows.Scan(&user.ID, &user.Name, &user.Email, &user.Password, &user.RoleID, &user.Role, &user.Profile, &user.IceSlug, &user.CreatedAt, &user.UpdatedAt)
}

func (s *Server) userByEmail(ctx context.Context, email string) (*db.User, error) {
	rows, err := s.db.QueryContext(ctx, `SELECT u.id,u.name,u.email,u.password,u.role_id,COALESCE(r.name,''),COALESCE(u.profile_pic,''),u.ice_slug,COALESCE(CAST(u.created_at AS CHAR),''),COALESCE(CAST(u.updated_at AS CHAR),'') FROM ice_users u LEFT JOIN ice_roles r ON r.id=u.role_id WHERE u.email=? LIMIT 1`, email)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	if !rows.Next() {
		return nil, nil
	}
	user := &db.User{}
	return user, rows.Scan(&user.ID, &user.Name, &user.Email, &user.Password, &user.RoleID, &user.Role, &user.Profile, &user.IceSlug, &user.CreatedAt, &user.UpdatedAt)
}

func (s *Server) login(w http.ResponseWriter, r *http.Request) {
	if r.Method == http.MethodGet {
		s.render(w, r, "Auth/Login", map[string]any{"canResetPassword": true, "canRegister": false, "status": ""})
		return
	}
	var input struct {
		Email    string `json:"email"`
		Password string `json:"password"`
	}
	readInput(r, &input)
	user, _ := s.userByEmail(r.Context(), input.Email)
	if user == nil || !db.CheckPassword(user.Password, input.Password) {
		s.renderValidation(w, r, "Auth/Login", map[string]any{"email": "These credentials do not match our records."})
		return
	}
	s.setSession(w, user.ID)
	redirect(w, r, "/dashboard")
}

func (s *Server) register(w http.ResponseWriter, r *http.Request) {
	if r.Method == http.MethodGet {
		s.render(w, r, "Auth/Register", map[string]any{})
		return
	}
	var input struct {
		Name                 string `json:"name"`
		Email                string `json:"email"`
		Password             string `json:"password"`
		PasswordConfirmation string `json:"password_confirmation"`
	}
	readInput(r, &input)
	errors := map[string]any{}
	if strings.TrimSpace(input.Name) == "" {
		errors["name"] = "The name field is required."
	}
	if !strings.Contains(input.Email, "@") {
		errors["email"] = "The email must be a valid email address."
	}
	if input.Password == "" || input.Password != input.PasswordConfirmation {
		errors["password"] = "The password confirmation does not match."
	}
	var exists int
	_ = s.db.QueryRowContext(r.Context(), `SELECT COUNT(*) FROM ice_users WHERE email=?`, input.Email).Scan(&exists)
	if exists > 0 {
		errors["email"] = "The email has already been taken."
	}
	if len(errors) > 0 {
		s.renderValidation(w, r, "Auth/Register", errors)
		return
	}
	hash, err := db.HashPassword(input.Password)
	if err != nil {
		s.renderValidation(w, r, "Auth/Register", map[string]any{"password": "Unable to register user."})
		return
	}
	res, err := s.db.ExecContext(r.Context(), `INSERT INTO ice_users (name,email,email_verified_at,password,role_id,ice_slug,created_at,updated_at) VALUES (?,?,NOW(),?,2,?,NOW(),NOW())`, input.Name, input.Email, hash, db.RandomSlug(20))
	if err != nil {
		s.renderValidation(w, r, "Auth/Register", map[string]any{"email": err.Error()})
		return
	}
	id, _ := res.LastInsertId()
	s.setSession(w, id)
	redirect(w, r, "/dashboard")
}

func (s *Server) apiLogin(w http.ResponseWriter, r *http.Request) {
	var input struct {
		Email    string `json:"email"`
		Password string `json:"password"`
	}
	readInput(r, &input)
	user, _ := s.userByEmail(r.Context(), input.Email)
	if user == nil || !db.CheckPassword(user.Password, input.Password) {
		writeJSON(w, http.StatusUnauthorized, map[string]any{"message": "Invalid credentials"})
		return
	}
	s.setSession(w, user.ID)
	id := strconv.FormatInt(user.ID, 10)
	writeJSON(w, http.StatusOK, map[string]any{"user": user, "token": id + "." + s.sign(id)})
}

func (s *Server) setSession(w http.ResponseWriter, userID int64) {
	id := strconv.FormatInt(userID, 10)
	http.SetCookie(w, &http.Cookie{Name: "iceburg_go_session", Value: id + "." + s.sign(id), Path: "/", HttpOnly: true, SameSite: http.SameSiteLaxMode, Expires: time.Now().Add(30 * 24 * time.Hour)})
}

func (s *Server) logout(w http.ResponseWriter, r *http.Request) {
	http.SetCookie(w, &http.Cookie{Name: "iceburg_go_session", Value: "", Path: "/", MaxAge: -1})
	redirect(w, r, "/login")
}

func (s *Server) forgotPassword(w http.ResponseWriter, r *http.Request) {
	if r.Method == http.MethodGet {
		s.render(w, r, "Auth/ForgotPassword", map[string]any{"status": ""})
		return
	}
	var input struct {
		Email string `json:"email"`
	}
	readInput(r, &input)
	if input.Email == "" || !strings.Contains(input.Email, "@") {
		s.renderValidation(w, r, "Auth/ForgotPassword", map[string]any{"email": "The email must be a valid email address."})
		return
	}
	token := db.RandomSlug(40)
	_, _ = s.db.ExecContext(r.Context(), `DELETE FROM ice_password_resets WHERE email=?`, input.Email)
	_, _ = s.db.ExecContext(r.Context(), `INSERT INTO ice_password_resets (email,token,created_at) VALUES (?,?,NOW())`, input.Email, token)
	s.render(w, r, "Auth/ForgotPassword", map[string]any{"status": "We have emailed your password reset link!", "token": token})
}

func (s *Server) resetPassword(w http.ResponseWriter, r *http.Request) {
	if r.Method == http.MethodGet {
		token := r.URL.Query().Get("token")
		if strings.HasPrefix(r.URL.Path, "/reset-password/") {
			token = strings.TrimPrefix(r.URL.Path, "/reset-password/")
		}
		s.render(w, r, "Auth/ResetPassword", map[string]any{"email": r.URL.Query().Get("email"), "token": token})
		return
	}
	var input struct {
		Email                string `json:"email"`
		Token                string `json:"token"`
		Password             string `json:"password"`
		PasswordConfirmation string `json:"password_confirmation"`
	}
	readInput(r, &input)
	if input.Email == "" || input.Token == "" || input.Password == "" || input.Password != input.PasswordConfirmation {
		s.renderValidation(w, r, "Auth/ResetPassword", map[string]any{"email": "This password reset token is invalid."})
		return
	}
	var count int
	_ = s.db.QueryRowContext(r.Context(), `SELECT COUNT(*) FROM ice_password_resets WHERE email=? AND token=? AND created_at >= DATE_SUB(NOW(), INTERVAL 2 HOUR)`, input.Email, input.Token).Scan(&count)
	if count == 0 {
		s.renderValidation(w, r, "Auth/ResetPassword", map[string]any{"email": "This password reset token is invalid."})
		return
	}
	hash, err := db.HashPassword(input.Password)
	if err != nil {
		s.renderValidation(w, r, "Auth/ResetPassword", map[string]any{"password": "Unable to reset password."})
		return
	}
	_, _ = s.db.ExecContext(r.Context(), `UPDATE ice_users SET password=?,remember_token=?,updated_at=NOW() WHERE email=?`, hash, db.RandomSlug(60), input.Email)
	_, _ = s.db.ExecContext(r.Context(), `DELETE FROM ice_password_resets WHERE email=?`, input.Email)
	s.render(w, r, "Auth/Login", map[string]any{"canResetPassword": true, "canRegister": false, "status": "Your password has been reset!"})
}

func (s *Server) confirmPassword(w http.ResponseWriter, r *http.Request) {
	if r.Method == http.MethodGet {
		s.render(w, r, "Auth/ConfirmPassword", map[string]any{})
		return
	}
	var input struct {
		Password string `json:"password"`
	}
	readInput(r, &input)
	user := s.currentUser(r)
	if user == nil || !db.CheckPassword(user.Password, input.Password) {
		s.renderValidation(w, r, "Auth/ConfirmPassword", map[string]any{"password": "The password is incorrect."})
		return
	}
	redirect(w, r, "/dashboard")
}

func (s *Server) verificationNotification(w http.ResponseWriter, r *http.Request) {
	s.render(w, r, "Auth/VerifyEmail", map[string]any{"status": "verification-link-sent"})
}

func (s *Server) render(w http.ResponseWriter, r *http.Request, component string, props map[string]any) {
	page := map[string]any{"component": component, "props": s.mergeShared(r, props), "url": r.URL.RequestURI(), "version": "go"}
	if r.Header.Get("X-Inertia") != "" {
		w.Header().Set("Vary", "Accept")
		w.Header().Set("X-Inertia", "true")
		writeJSON(w, http.StatusOK, page)
		return
	}
	raw, _ := json.Marshal(page)
	w.Header().Set("Content-Type", "text/html; charset=utf-8")
	_ = s.tpl.Execute(w, map[string]any{"Title": s.cfg.Name, "Theme": db.Setting(r.Context(), s.db, "theme"), "Page": template.JS(raw), "RouteJS": template.JS(routeJS)})
}

func (s *Server) renderValidation(w http.ResponseWriter, r *http.Request, component string, errors map[string]any) {
	if r.Header.Get("X-Inertia") != "" {
		w.Header().Set("X-Inertia", "true")
		w.WriteHeader(http.StatusUnprocessableEntity)
		_ = json.NewEncoder(w).Encode(map[string]any{"errors": errors})
		return
	}
	s.render(w, r, component, map[string]any{"errors": errors})
}

func (s *Server) mergeShared(r *http.Request, props map[string]any) map[string]any {
	for k, v := range s.shared(r) {
		if _, exists := props[k]; !exists {
			props[k] = v
		}
	}
	if _, ok := props["errors"]; !ok {
		props["errors"] = map[string]any{}
	}
	return props
}

func (s *Server) shared(r *http.Request) map[string]any {
	user := s.currentUser(r)
	if user != nil {
		user.Password = ""
	}
	return map[string]any{
		"auth": map[string]any{
			"user":            user,
			"openai":          os.Getenv("OPENAI_API_KEY") != "",
			"system_settings": s.settingsMap(r.Context()),
			"modules":         s.moduleGroups(r.Context()),
		},
		"ziggy": map[string]any{"url": s.cfg.AppURL, "location": r.URL.String(), "routes": map[string]any{}},
	}
}

func (s *Server) settingsMap(ctx context.Context) map[string]any {
	rows, err := s.db.QueryContext(ctx, `SELECT name,value,COALESCE(additional_data,'') FROM ice_settings`)
	if err != nil {
		return map[string]any{"theme": "light"}
	}
	defer rows.Close()
	out := map[string]any{}
	for rows.Next() {
		var key, value, extra string
		_ = rows.Scan(&key, &value, &extra)
		out[key] = value
		if key == "logo" && extra != "" {
			out["logo"] = extra
		}
	}
	if out["theme"] == nil {
		out["theme"] = "light"
	}
	return out
}

func (s *Server) moduleGroups(ctx context.Context) []map[string]any {
	groups, _ := queryMaps(ctx, s.db, `SELECT * FROM ice_module_groups WHERE status=1 ORDER BY view_order,id`)
	for _, group := range groups {
		id := group["id"]
		modules, _ := queryMaps(ctx, s.db, `SELECT * FROM ice_modules WHERE status=1 AND module_group_id=? ORDER BY view_order,id`, id)
		group["modules"] = modules
	}
	return groups
}

func (s *Server) simplePage(component string, props map[string]any) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		s.render(w, r, component, clone(props))
	}
}

func appURL(path string) string {
	if path == "" {
		return "#"
	}
	return path
}

func redirect(w http.ResponseWriter, r *http.Request, target string) {
	if r.Header.Get("X-Inertia") != "" || wantsJSON(r) {
		w.Header().Set("X-Inertia-Location", target)
		w.WriteHeader(http.StatusConflict)
		return
	}
	http.Redirect(w, r, target, http.StatusFound)
}

func readInput(r *http.Request, out any) {
	ct := r.Header.Get("Content-Type")
	if strings.Contains(ct, "application/json") {
		_ = json.NewDecoder(r.Body).Decode(out)
		return
	}
	_ = r.ParseMultipartForm(32 << 20)
	raw, _ := json.Marshal(r.Form)
	flat := map[string]any{}
	var forms map[string][]string
	_ = json.Unmarshal(raw, &forms)
	for key, values := range forms {
		if len(values) == 1 {
			flat[key] = values[0]
		} else {
			flat[key] = values
		}
	}
	again, _ := json.Marshal(flat)
	_ = json.Unmarshal(again, out)
}

func formMap(r *http.Request) map[string]any {
	var obj map[string]any
	if strings.Contains(r.Header.Get("Content-Type"), "application/json") {
		_ = json.NewDecoder(r.Body).Decode(&obj)
		if obj != nil {
			return obj
		}
	}
	_ = r.ParseMultipartForm(32 << 20)
	out := map[string]any{}
	for key, values := range r.Form {
		if len(values) == 1 {
			out[key] = values[0]
		} else {
			out[key] = values
		}
	}
	return out
}

func writeJSON(w http.ResponseWriter, status int, value any) {
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(status)
	_ = json.NewEncoder(w).Encode(value)
}

func wantsJSON(r *http.Request) bool {
	return strings.Contains(r.Header.Get("Accept"), "application/json") || strings.HasPrefix(r.URL.Path, "/api/") || strings.HasPrefix(r.URL.Path, "/data") || strings.HasPrefix(r.URL.Path, "/admin_data")
}

func queryMaps(ctx context.Context, database *sql.DB, query string, args ...any) ([]map[string]any, error) {
	rows, err := database.QueryContext(ctx, query, args...)
	if err != nil {
		return nil, err
	}
	return db.RowsToMaps(rows)
}

func clone(in map[string]any) map[string]any {
	out := map[string]any{}
	for k, v := range in {
		out[k] = v
	}
	return out
}

const appHTML = `<!DOCTYPE html>
<html data-theme="{{.Theme}}" lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{.Title}}</title>
  <link rel="stylesheet" href="/css/app.css">
  <script>{{.RouteJS}}</script>
  <script src="/js/app.js" defer></script>
</head>
<body class="font-sans antialiased">
  <div id="app" data-page='{{.Page}}'></div>
</body>
</html>`

const routeJS = `window.route=function(name, params){var r={
home:'/',login:'/login',register:'/register',logout:'/logout',dashboard:'/dashboard',import:'/import',settings:'/settings',data:'/admin/data',connectors:'/admin/connectors',scheduler:'/admin/scheduler',builder:'/admin/builder',
'password.request':'/forgot-password','password.email':'/forgot-password','password.reset':'/reset-password','password.update':'/reset-password','password.confirm':'/confirm-password','verification.send':'/email/verification-notification'
}; return r[name] || '/';};`
