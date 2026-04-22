package main

import (
	"context"
	"log"
	"time"
	"os"
	"strconv"

	ctt "myhomehub/go/grpc/gen/ctt"

	"google.golang.org/grpc"
	"google.golang.org/grpc/credentials/insecure"
)

func main() {

	// Connexion au serveur gRPC
	conn, err := grpc.Dial(
		"grpc-ctt:50051",
		grpc.WithTransportCredentials(insecure.NewCredentials()),
	)
	if err != nil {
		log.Fatalf("connection failed: %v", err)
	}
	defer conn.Close()

	// Création du client
	client := ctt.NewCttServiceClient(conn)

	// Timeout (très important en gRPC)
	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()

	licenseStr := os.Getenv("LICENSE_ID")

	if licenseStr == "" {
		log.Fatal("LICENSE_ID environment variable not set")
	}

	licenseInt, err := strconv.Atoi(licenseStr)
	if err != nil {
		log.Fatalf("Invalid LICENSE_ID: %v", err)
	}

	// Requête
	req := &ctt.GetPlayerResultRequest{
		License: int32(licenseInt),
	}

	// Appel RPC
	res, err := client.GetPlayerResult(ctx, req)
	if err != nil {
		log.Fatalf("rpc error: %v", err)
	}

	result := res.GetResult()

	log.Println("===== Player Result =====")
	log.Printf("Total   : %d", result.GetTotal())
	log.Printf("Win     : %d", result.GetWin())
	log.Printf("Lost    : %d", result.GetLost())
	log.Printf("Winrate : %d%%", result.GetWinrate())
}