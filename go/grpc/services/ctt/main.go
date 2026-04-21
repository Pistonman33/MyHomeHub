package main

import (
	"context"
	"database/sql"
	"fmt"
	"log"
	"net"
	"os"
	"time"

	"myhomehub/go/grpc/gen/ctt"

	_ "github.com/go-sql-driver/mysql"

	"google.golang.org/grpc"
	"google.golang.org/grpc/codes"
	"google.golang.org/grpc/reflection"
	"google.golang.org/grpc/status"
)

type cttServer struct {
	ctt.UnimplementedCttServiceServer
	db *sql.DB
}

func (s *cttServer) GetPlayerResult(
	ctx context.Context,
	req *ctt.GetPlayerResultRequest,
) (*ctt.GetPlayerResultResponse, error) {

	if req.GetLicense() == 0 {
		return nil, status.Error(
			codes.InvalidArgument,
			"license is required",
		)
	}

	var wins, losses int32
	

	err := s.db.QueryRowContext(ctx, `
		SELECT
			COALESCE(SUM(CASE WHEN result = 'V' THEN 1 ELSE 0 END),0),
			COALESCE(SUM(CASE WHEN result = 'D' THEN 1 ELSE 0 END),0)
		FROM myhome_ctt_matches
		INNER JOIN myhome_ctt_players ON myhome_ctt_players.license = myhome_ctt_matches.player_license
		WHERE myhome_ctt_players.license = ?
	`,req.GetLicense()).Scan(&wins, &losses)

	if err != nil {
		log.Printf("[ctt] query error: %v", err)
		return nil, status.Errorf(codes.Internal, "database error")
	}

	total := wins + losses

	if(total == 0) {
		return nil, status.Error(
			codes.NotFound,
			"no matches found for this license",
		)
	}
	
	var winrate int32
	if total > 0 {
		winrate = int32(float64(wins) / float64(total) * 100)
	}

	return &ctt.GetPlayerResultResponse{
		Result: &ctt.Result{
			Total:   total,
			Win:     wins,
			Lost:    losses,
			Winrate: winrate,
		},
	}, nil
}

func main() {

	dsn := fmt.Sprintf("%s:%s@tcp(%s:%s)/%s?parseTime=true",
		getEnv("DB_USERNAME", "root"),
		getEnv("DB_PASSWORD", ""),
		getEnv("DB_HOST", "mysql"),
		getEnv("DB_PORT", "3306"),
		getEnv("DB_DATABASE", "myhomehub"),
	)

	var db *sql.DB
	var err error

	for i := 0; i < 10; i++ {
		db, err = sql.Open("mysql", dsn)
		if err == nil && db.Ping() == nil {
			break
		}

		log.Printf("[ctt] waiting for MySQL... (%d/10): %v", i+1, err)
		time.Sleep(3 * time.Second)
	}

	if err != nil {
		log.Fatalf("[ctt] cannot connect to MySQL: %v", err)
	}

	db.SetMaxOpenConns(10)
	db.SetMaxIdleConns(5)
	db.SetConnMaxLifetime(5 * time.Minute)

	port := getEnv("APP_PORT", "50051")

	lis, err := net.Listen("tcp", ":"+port)
	if err != nil {
		log.Fatalf("[ctt] failed to listen: %v", err)
	}

	srv := grpc.NewServer()
	reflection.Register(srv)

	ctt.RegisterCttServiceServer(
		srv,
		&cttServer{db: db},
	)

	log.Printf("[ctt] gRPC server listening on :%s", port)

	if err := srv.Serve(lis); err != nil {
		log.Fatalf("[ctt] failed to serve: %v", err)
	}
}

func getEnv(key, fallback string) string {
	if v := os.Getenv(key); v != "" {
		return v
	}
	return fallback
}