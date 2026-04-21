# ───────────── BUILD STAGE ─────────────
FROM golang:1.23-alpine

# Tools needed for protobuf generation
RUN apk add --no-cache protobuf git

# Plugins protobuf Go
RUN go install google.golang.org/protobuf/cmd/protoc-gen-go@v1.34.2 \
 && go install google.golang.org/grpc/cmd/protoc-gen-go-grpc@v1.4.0

WORKDIR /app

# 1️⃣ Copy go.mod first (cache Docker)
COPY go.mod go.sum ./
# download deps (cache Docker)
RUN go mod download

# 2️⃣ Copy all project
COPY . .

# 3️⃣ Generate protobuf files on code code.
RUN mkdir -p gen/ctt && \
    protoc \
      --go_out=gen/ctt --go_opt=paths=source_relative \
      --go-grpc_out=gen/ctt --go-grpc_opt=paths=source_relative \
      -I proto/ctt \
      proto/ctt/player_result.proto

# 4️⃣ Clean deps
RUN go mod tidy

# ───────────── RUN ─────────────
CMD ["go", "run", "services/ctt/client/client.go"]