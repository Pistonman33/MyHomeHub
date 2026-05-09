package main

import (
	"context"
	"io"
	"log"
	"net/http"
	"os"
	"os/exec"
	"path/filepath"
	"strings"
	"time"

	pb "myhomehub/go/grpc/gen/video"

	"google.golang.org/grpc"
	"google.golang.org/grpc/credentials/insecure"
)

var videoClient pb.VideoServiceClient

// ------------------------------------------------
// ENV
// ------------------------------------------------

func getEnv(key, fallback string) string {
	if v := os.Getenv(key); v != "" {
		return v
	}
	return fallback
}

// ------------------------------------------------
// PATHS
// ------------------------------------------------

func hlsDir(id string) string {
	return filepath.Join("videos", id)
}

func hlsIndex(id string) string {
	return filepath.Join("videos", id, "index.m3u8")
}

func rawPath(id string) string {
	return filepath.Join("storage", id+".mpg")
}

func normalizedPath(id string) string {
	return filepath.Join("storage", id+"_normalized.mp4")
}

// ------------------------------------------------
// CORS
// ------------------------------------------------

func cors(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {

		w.Header().Set("Access-Control-Allow-Origin", "*")
		w.Header().Set("Access-Control-Allow-Headers", "*")
		w.Header().Set("Access-Control-Allow-Methods", "GET, OPTIONS")

		if r.Method == "OPTIONS" {
			return
		}

		next.ServeHTTP(w, r)
	})
}

// ------------------------------------------------
// gRPC DOWNLOAD
// ------------------------------------------------

func downloadVideo(ctx context.Context, id string) (string, error) {

	output := rawPath(id)
	_ = os.MkdirAll("storage", 0755)

	stream, err := videoClient.StreamVideo(ctx, &pb.VideoRequest{
		Name: id + ".mpg",
	})
	if err != nil {
		return "", err
	}

	file, err := os.Create(output)
	if err != nil {
		return "", err
	}
	defer file.Close()

	for {
		chunk, err := stream.Recv()
		if err == io.EOF {
			break
		}
		if err != nil {
			return "", err
		}
		file.Write(chunk.Data)
	}

	return output, nil
}

// ------------------------------------------------
// NORMALIZATION
// ------------------------------------------------

func normalizeVideo(input, id string) (string, error) {

	output := normalizedPath(id)

	cmd := exec.Command(
		"ffmpeg",
		"-y",
		"-fflags", "+genpts",
		"-i", input,
		"-c:v", "libx264",
		"-preset", "veryfast",
		"-pix_fmt", "yuv420p",
		"-profile:v", "main",
		"-c:a", "aac",
		"-b:a", "128k",
		"-af", "aresample=async=1",
		"-avoid_negative_ts", "make_zero",
		output,
	)

	cmd.Stdout = os.Stdout
	cmd.Stderr = os.Stderr

	log.Println("Normalizing:", id)

	return output, cmd.Run()
}

// ------------------------------------------------
// HLS GENERATION
// ------------------------------------------------

func generateHLS(input, id string) error {

	outputDir := hlsDir(id)
	_ = os.MkdirAll(outputDir, 0755)

	cmd := exec.Command(
		"ffmpeg",
		"-y",
		"-i", input,

		"-c:v", "libx264",
		"-preset", "veryfast",
		"-g", "48",
		"-keyint_min", "48",
		"-sc_threshold", "0",

		"-c:a", "aac",
		"-b:a", "128k",

		"-f", "hls",
		"-hls_time", "4",
		"-hls_playlist_type", "vod",
		"-hls_flags", "independent_segments",

		"-hls_segment_filename",
		filepath.Join(outputDir, "seg_%03d.ts"),

		filepath.Join(outputDir, "index.m3u8"),
	)

	cmd.Stdout = os.Stdout
	cmd.Stderr = os.Stderr

	log.Println("Generating HLS:", id)

	return cmd.Run()
}

// ------------------------------------------------
// STREAM HANDLER (FIXÉ PROPREMENT)
// ------------------------------------------------

func videoHandler(w http.ResponseWriter, r *http.Request) {

	path := r.URL.Path

	// ------------------------------------------------
	// 1. SERVE TS FILES DIRECTLY
	// ------------------------------------------------
	if strings.HasSuffix(path, ".ts") {
		file := filepath.Join("videos", strings.TrimPrefix(path, "/video/"))
		http.ServeFile(w, r, file)
		return
	}

	// ------------------------------------------------
	// 2. EXTRACT ID
	// ------------------------------------------------
	parts := strings.Split(path, "/")
	if len(parts) < 3 {
		http.Error(w, "missing id", 400)
		return
	}

	id := parts[2]
	index := hlsIndex(id)

	// ------------------------------------------------
	// 3. CACHE HIT
	// ------------------------------------------------
	if _, err := os.Stat(index); err == nil {
		w.Header().Set("Content-Type", "application/vnd.apple.mpegurl")
		http.ServeFile(w, r, index)
		return
	}

	log.Println("Cache miss → generating", id)

	// ------------------------------------------------
	// 4. PIPELINE
	// ------------------------------------------------
	ctx, cancel := context.WithTimeout(r.Context(), 5*time.Minute)
	defer cancel()

	raw, err := downloadVideo(ctx, id)
	if err != nil {
		http.Error(w, err.Error(), 500)
		return
	}

	norm, err := normalizeVideo(raw, id)
	if err != nil {
		http.Error(w, err.Error(), 500)
		return
	}

	if err := generateHLS(norm, id); err != nil {
		http.Error(w, err.Error(), 500)
		return
	}

	w.Header().Set("Content-Type", "application/vnd.apple.mpegurl")
	http.ServeFile(w, r, index)
}

// ------------------------------------------------
// MAIN
// ------------------------------------------------

func main() {

	port := getEnv("APP_PORT", "8085")

	conn, err := grpc.Dial(
		"grpc-video:50052",
		grpc.WithTransportCredentials(insecure.NewCredentials()),
	)
	if err != nil {
		log.Fatal(err)
	}

	videoClient = pb.NewVideoServiceClient(conn)

	mux := http.NewServeMux()

	// IMPORTANT
	mux.HandleFunc("/video/", videoHandler)

	log.Println("Video Gateway running on", port)

	handler := cors(mux)

	log.Fatal(http.ListenAndServe(":"+port, handler))
}