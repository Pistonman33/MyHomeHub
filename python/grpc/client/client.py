import grpc
import os

import player_result_pb2 as ctt_pb2
import player_result_pb2_grpc as ctt_pb2_grpc

license_id = int(os.getenv("LICENSE_ID", "0"))

# Nom du service docker-compose
channel = grpc.insecure_channel("grpc-ctt:50051")

# Stub gRPC
stub = ctt_pb2_grpc.CttServiceStub(channel)

# Request
request = ctt_pb2.GetPlayerResultRequest(
    license=license_id
)

# Call RPC
response = stub.GetPlayerResult(request)

# Result
result = response.result

print("=== Player Result ===")
print(f"Total   : {result.total}")
print(f"Win     : {result.win}")
print(f"Lost    : {result.lost}")
print(f"Winrate : {result.winrate}%")
