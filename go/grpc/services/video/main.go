package main

import (
	"io"
	"log"
	"net"
	"os"

	pb "myhomehub/go/grpc/gen/video"

	"google.golang.org/grpc"
)

type server struct {
	pb.UnimplementedVideoServiceServer
}

func (s *server) StreamVideo(
	req *pb.VideoRequest,
	stream pb.VideoService_StreamVideoServer,
) error {

	path := req.Name
	file, err := os.Open("files/" + path)
	if err != nil {
		return err
	}
	defer file.Close()

	buffer := make([]byte, 1024*64) // 64KB

	for {
		n, err := file.Read(buffer)

		if err == io.EOF {
			break
		}
		if err != nil {
			return err
		}

		chunk := &pb.VideoChunk{
			Data: buffer[:n],
		}

		if err := stream.Send(chunk); err != nil {
			return err
		}
	}

	log.Println("Streaming finished")
	return nil
}

func main() {

	port := os.Getenv("APP_PORT")
	if port == "" {
		port = "50052" // fallback si non défini
	}

	lis, err := net.Listen("tcp", ":"+port)
	if err != nil {
		log.Fatal(err)
	}

	grpcServer := grpc.NewServer()

	pb.RegisterVideoServiceServer(grpcServer, &server{})

	log.Printf("Streaming server started on :%s\n", port)

	grpcServer.Serve(lis)
}