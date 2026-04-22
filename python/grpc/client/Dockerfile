FROM python:3.11-slim

WORKDIR /app

# Install Python gRPC / protobuf
COPY python/grpc/client/requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# copy source code python
COPY python/grpc/client/client.py /app/client.py

# copy proto GO files
COPY go/grpc/proto/ctt /proto

# create lib directory for generated code
RUN mkdir -p /app/lib

# compiler protobuf
RUN python -m grpc_tools.protoc \
    -I/proto \
    --python_out=/app/lib \
    --grpc_python_out=/app/lib \
    /proto/*.proto

# Ajouter lib au PYTHONPATH
ENV PYTHONPATH="/app/lib"

EXPOSE 2080

CMD ["python", "client.py"]
