package main

import (
	"context"
	"fmt"
	"log"
	"net/http"
	"os"
	"os/signal"
	"syscall"
	"time"

	"iceburgcrm/goversion/internal/app"
	"iceburgcrm/goversion/internal/db"
	"iceburgcrm/goversion/internal/web"
)

func main() {
	cfg := app.LoadConfig()
	database, err := db.Open(cfg)
	if err != nil {
		log.Fatal(err)
	}
	defer database.Close()

	cmd := "serve"
	if len(os.Args) > 1 {
		cmd = os.Args[1]
	}

	ctx := context.Background()
	switch cmd {
	case "migrate":
		must(db.Migrate(ctx, database))
		must(db.Seed(ctx, database))
		must(db.GenerateModuleTables(ctx, database))
	case "seed":
		must(db.Seed(ctx, database))
	case "generate-modules":
		must(db.GenerateModuleTables(ctx, database))
	case "serve":
		server := &http.Server{
			Addr:              cfg.Addr,
			Handler:           web.NewServer(cfg, database).Routes(),
			ReadHeaderTimeout: 10 * time.Second,
		}
		go func() {
			log.Printf("Iceburg CRM Go listening on %s", cfg.Addr)
			if err := server.ListenAndServe(); err != nil && err != http.ErrServerClosed {
				log.Fatal(err)
			}
		}()
		stop := make(chan os.Signal, 1)
		signal.Notify(stop, os.Interrupt, syscall.SIGTERM)
		<-stop
		shutdownCtx, cancel := context.WithTimeout(context.Background(), 10*time.Second)
		defer cancel()
		must(server.Shutdown(shutdownCtx))
	default:
		fmt.Fprintf(os.Stderr, "unknown command %q\n", cmd)
		os.Exit(2)
	}
}

func must(err error) {
	if err != nil {
		log.Fatal(err)
	}
}
