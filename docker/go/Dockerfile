# ── Build stage ────────────────────────────────────────────────
FROM golang:1.23-alpine AS builder

RUN apk --no-cache add protobuf && \
    go install google.golang.org/protobuf/cmd/protoc-gen-go@v1.34.2 && \
    go install google.golang.org/grpc/cmd/protoc-gen-go-grpc@v1.4.0

WORKDIR /app

# Source code copy
COPY go/grpc/ .

# protobuf file generate file for go code
RUN mkdir -p gen/ctt && \
    protoc \
      --go_out=gen/ctt      --go_opt=paths=source_relative \
      --go-grpc_out=gen/ctt --go-grpc_opt=paths=source_relative \
      -I proto/ctt \
      proto/ctt/player_result.proto

# go mod tidy to import all library neede for the project and create go.sum
RUN go mod tidy

# Build the application with optimizations for size and performance
RUN CGO_ENABLED=0 GOOS=linux go build \
      -ldflags="-w -s" \
      -o ctt-service \
      ./services/ctt

# ── Run stage ──────────────────────────────────────────────────
FROM alpine:3.20

RUN apk --no-cache add ca-certificates tzdata

WORKDIR /app

COPY --from=builder /app/ctt-service .

EXPOSE 50051

CMD ["./ctt-service"]