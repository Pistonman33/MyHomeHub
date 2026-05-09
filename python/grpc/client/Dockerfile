FROM python:3.11-slim

WORKDIR /app

# Install Python gRPC / protobuf
COPY python/grpc/client/requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

RUN apt-get update && apt-get install -y \
    ffmpeg \
    && rm -rf /var/lib/apt/lists/*

# copy source code python
COPY python/grpc/client/ /app/

# copy proto GO files
COPY go/grpc/proto /proto

# create lib directory for generated code
RUN mkdir -p /app/lib

# compiler protobuf
RUN python -m grpc_tools.protoc \
    -I/proto \
    --python_out=/app/lib \
    --grpc_python_out=/app/lib \
    $(find /proto -name "*.proto")

# Ajouter lib au PYTHONPATH
ENV PYTHONPATH="/app/lib:/app"

EXPOSE 2080

CMD ["tail", "-f", "/dev/null"]
