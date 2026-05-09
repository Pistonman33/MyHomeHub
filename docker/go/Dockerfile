# ── Build stage ────────────────────────────────────────────────
FROM golang:1.23-alpine AS builder

ARG SERVICE
ARG PORT

RUN apk --no-cache add protobuf

RUN go install google.golang.org/protobuf/cmd/protoc-gen-go@v1.34.2 && \
    go install google.golang.org/grpc/cmd/protoc-gen-go-grpc@v1.4.0

WORKDIR /app

COPY go/grpc/go.mod go/grpc/go.sum ./
RUN go mod download

COPY go/grpc/ .

# Generate ALL proto files
RUN find proto -name "*.proto" > protos.txt && \
    protoc \
      --go_out=gen --go_opt=paths=source_relative \
      --go-grpc_out=gen --go-grpc_opt=paths=source_relative \
      -I proto \
      $(cat protos.txt)
      
RUN go mod tidy

# Build selected service
RUN CGO_ENABLED=0 GOOS=linux go build \
      -ldflags="-w -s" \
      -o service \
      ./services/${SERVICE}

# ── Run stage ──────────────────────────────────────────────────
FROM alpine:3.20

RUN apk --no-cache add ca-certificates tzdata ffmpeg

WORKDIR /app

COPY --from=builder /app/service .

EXPOSE ${PORT}
ENV APP_PORT=${PORT}

CMD ["./service"]