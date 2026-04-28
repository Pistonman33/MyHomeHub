<?php
// GENERATED CODE -- DO NOT EDIT!

namespace App\Grpc\Ctt;

/**
 * ── Service ─────────────────────────────────────────────────────
 *
 */
class CttServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \App\Grpc\Ctt\GetPlayerResultRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetPlayerResult(\App\Grpc\Ctt\GetPlayerResultRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/ctt.CttService/GetPlayerResult',
        $argument,
        ['\App\Grpc\Ctt\GetPlayerResultResponse', 'decode'],
        $metadata, $options);
    }

}
