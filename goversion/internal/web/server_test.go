package web

import (
	"context"
	"net/http"
	"net/http/httptest"
	"strings"
	"testing"

	"iceburgcrm/goversion/internal/db"
)

func TestAdminOnlyAllowsAdmin(t *testing.T) {
	server := &Server{}
	called := false
	handler := server.adminOnly(func(w http.ResponseWriter, r *http.Request) {
		called = true
		w.WriteHeader(http.StatusNoContent)
	})
	req := httptest.NewRequest(http.MethodGet, "/admin", nil)
	req = req.WithContext(context.WithValue(req.Context(), userKey, &db.User{Role: "Admin"}))
	rec := httptest.NewRecorder()

	handler(rec, req)

	if !called {
		t.Fatal("admin handler was not called")
	}
	if rec.Code != http.StatusNoContent {
		t.Fatalf("status = %d, want %d", rec.Code, http.StatusNoContent)
	}
}

func TestAdminOnlyRejectsNonAdminJSON(t *testing.T) {
	server := &Server{}
	handler := server.adminOnly(func(w http.ResponseWriter, r *http.Request) {
		t.Fatal("non-admin request reached handler")
	})
	req := httptest.NewRequest(http.MethodGet, "/admin", nil)
	req.Header.Set("Accept", "application/json")
	req = req.WithContext(context.WithValue(req.Context(), userKey, &db.User{Role: "User"}))
	rec := httptest.NewRecorder()

	handler(rec, req)

	if rec.Code != http.StatusForbidden {
		t.Fatalf("status = %d, want %d", rec.Code, http.StatusForbidden)
	}
	if !strings.Contains(rec.Body.String(), "No Access") {
		t.Fatalf("body %q does not contain No Access", rec.Body.String())
	}
}

func TestRecoverHandlesOptionsAndSecurityHeaders(t *testing.T) {
	server := &Server{}
	handler := server.recover(http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		t.Fatal("OPTIONS should be handled before downstream handler")
	}))
	req := httptest.NewRequest(http.MethodOptions, "/data/settings", nil)
	rec := httptest.NewRecorder()

	handler.ServeHTTP(rec, req)

	if rec.Code != http.StatusNoContent {
		t.Fatalf("status = %d, want %d", rec.Code, http.StatusNoContent)
	}
	if got := rec.Header().Get("X-Frame-Options"); got != "SAMEORIGIN" {
		t.Fatalf("X-Frame-Options = %q", got)
	}
	if got := rec.Header().Get("X-Content-Type-Options"); got != "nosniff" {
		t.Fatalf("X-Content-Type-Options = %q", got)
	}
}

func TestIsAdminDataPath(t *testing.T) {
	adminPaths := []string{"permissions", "builder/1/type/save", "connector/set_connector", "connectors", "endpoints/1", "commands/run/1", "workflow/save"}
	for _, path := range adminPaths {
		if !isAdminDataPath(path) {
			t.Fatalf("%q should be admin-only", path)
		}
	}
	publicPaths := []string{"search_data", "save", "subpanel/save", "download/1/csv", "help", "settings"}
	for _, path := range publicPaths {
		if isAdminDataPath(path) {
			t.Fatalf("%q should not be admin-only", path)
		}
	}
}
