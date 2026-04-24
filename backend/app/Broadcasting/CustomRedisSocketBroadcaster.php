<?php

namespace App\Broadcasting;

use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

class CustomRedisSocketBroadcaster extends Broadcaster
{
    protected $nodeServer;

    public function __construct(array $config)
    {
        $this->nodeServer = $config['node_server'] ?? 'http://localhost:4000';
    }

    public function broadcast(array $channels, $event, array $payload = [])
    {
        $socket = $this->nodeServer . '/broadcast';

        foreach ($channels as $channel) {
            Http::post($socket, [
                'channel' => $channel,
                'event' => $event,
                'data' => $payload
            ]);
        }

        return true;
    }

    public function auth($request)
    {
        return true;
    }

    public function validAuthenticationResponse($request, $result)
    {
        return true;
    }
}