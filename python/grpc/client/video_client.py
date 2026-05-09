import grpc

from video import video_pb2 as video_pb2
from video import video_pb2_grpc as video_pb2_grpc

import os

VIDEO_SERVICE = os.getenv("VIDEO_SERVICE", "grpc-video:50052")

def stream_video():
    channel = grpc.insecure_channel(VIDEO_SERVICE)
    stub = video_pb2_grpc.VideoServiceStub(channel)

    request = video_pb2.VideoRequest(
        name="Goldorak.mpg"
    )

    stream = stub.StreamVideo(request)

    with open("/app/output.mp4", "wb") as f:
        for chunk in stream:
            print(f"chunk: {len(chunk.data)} bytes")
            f.write(chunk.data)

    print("Video saved")

if __name__ == "__main__":
    stream_video()