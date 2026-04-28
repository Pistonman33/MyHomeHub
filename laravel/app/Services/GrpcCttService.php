<?php

namespace App\Services;

use App\Grpc\Ctt\CttServiceClient;
use App\Grpc\Ctt\GetPlayerResultRequest;

class GrpcCttService
{
    protected $client;

    public function __construct()
    {
        $this->client = new CttServiceClient('grpc-ctt:50051', [
            'credentials' => \Grpc\ChannelCredentials::createInsecure(),
        ]);
    }

    public function getPlayerResult(int $licenseId)
    {
        $request = new GetPlayerResultRequest();
        $request->setLicense($licenseId);

        list($response, $status) = $this->client->GetPlayerResult($request)->wait();

        if ($status->code !== \Grpc\STATUS_OK) {
            throw new \Exception('gRPC error: ' . $status->details);
        }

        return $response->getResult();
    }
}