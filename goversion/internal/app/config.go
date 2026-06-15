package app

import (
	"bufio"
	"os"
	"strings"
)

type Config struct {
	Name     string
	Addr     string
	AppKey   string
	AppURL   string
	DBHost   string
	DBPort   string
	DBName   string
	DBUser   string
	DBPass   string
	PublicFS string
}

func LoadConfig() Config {
	loadDotEnv(".env")
	return Config{
		Name:     env("APP_NAME", "IceburgCRM"),
		Addr:     env("APP_ADDR", ":8090"),
		AppKey:   env("APP_KEY", "iceburg-go-dev-key"),
		AppURL:   env("APP_URL", "http://localhost:8090"),
		DBHost:   env("DB_HOST", "127.0.0.1"),
		DBPort:   env("DB_PORT", "3306"),
		DBName:   env("DB_DATABASE", "iceburg"),
		DBUser:   env("DB_USERNAME", "iceburg_user"),
		DBPass:   env("DB_PASSWORD", "secret"),
		PublicFS: env("PUBLIC_PATH", "public"),
	}
}

func env(key, fallback string) string {
	if value := os.Getenv(key); value != "" {
		return value
	}
	return fallback
}

func loadDotEnv(path string) {
	file, err := os.Open(path)
	if err != nil {
		return
	}
	defer file.Close()
	scanner := bufio.NewScanner(file)
	for scanner.Scan() {
		line := strings.TrimSpace(scanner.Text())
		if line == "" || strings.HasPrefix(line, "#") || !strings.Contains(line, "=") {
			continue
		}
		parts := strings.SplitN(line, "=", 2)
		key := strings.TrimSpace(parts[0])
		value := strings.Trim(strings.TrimSpace(parts[1]), `"'`)
		if os.Getenv(key) == "" {
			_ = os.Setenv(key, value)
		}
	}
}
