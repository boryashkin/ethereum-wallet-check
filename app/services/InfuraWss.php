<?php
namespace app\services;

class InfuraWss
{
    public static function getApiUrl()
    {
        return 'wss://' . getenv('INFRA_NETWORK') . '.infura.io/ws/v3/' . getenv('INFRA_PROJECT_ID');
    }

    public static function getEthSubscribeMessage($accoundNumbers): string
    {
        return strtr(
            '{"jsonrpc":"2.0", "id": 1, "method": "eth_subscribe", "params": ["logs", {"address": %array}]}',
            ['%array' => \json_encode($accoundNumbers)]
        );
    }
}
