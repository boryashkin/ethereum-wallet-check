<?php
namespace app\services;

use app\containers\App;
use app\dataTypes\Wei;

class InfuraJsonRpc
{
    public const JSON_RPC = '2.0';

    public function ethGetBalance($address, $defaultBlock = 'latest'): Wei
    {
        $result = $this->call('eth_getBalance', ['params' => [$address, $defaultBlock], 'id' => 1]);
        $result = \json_decode($result, true);

        if (isset($result['error'])) {
            throw new \Exception($result['message'], $result['code']);
        }

        return new Wei($result['result']);
    }

    public function ethBlockNumber()
    {
        $result = $this->call('eth_blockNumber', ['params' => [], 'id' => 1]);
        $result = \json_decode($result, true);

        if (isset($result['error'])) {
            throw new \Exception($result['message'], $result['code']);
        }

        return new Wei($result['result']);
    }

    public function ethGetTransactionByHash($hash):? array
    {
        $result = $this->call('eth_getTransactionByHash', ['params' => [$hash], 'id' => 1]);
        $result = \json_decode($result, true);

        if (isset($result['error'])) {
            throw new \Exception($result['message'], $result['code']);
        }

        return $result['result'];
    }

    private function call($method, array $data)
    {
        $data['method'] = $method;
        $data['jsonrpc'] = self::JSON_RPC;
        $ch = curl_init($this->getApiUrl());
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, \json_encode($data));
        if (App::isDebug()) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        }
        $output = curl_exec($ch);
        if (App::isDebug()) {
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($output, 0, $headerSize);
            $body = substr($output, $headerSize);
            $headerSent = curl_getinfo($ch, CURLINFO_HEADER_OUT );
            $bodySent = http_build_query($data);

            if ($output === false) {
                error_log("CURL error: " . curl_error($ch));
            }
            error_log("INFRA_JSON_API_URL: " . $this->getApiUrl());
            error_log("CURL header sent: " . $headerSent);
            error_log("CURL body sent: " . $bodySent);
            error_log("CURL header: " . $header);
            error_log("CURL body: " . $body);
            error_log("CURL output: " . $output);
            error_log("CURL CURLOPT_POSTFIELDS: " . \json_encode($data));
            $output = $body;
        }
        curl_close($ch);

        return $output;
    }

    private function getApiUrl()
    {
        return 'https://' . getenv('INFRA_NETWORK') . '.infura.io/v3/' . getenv('INFRA_PROJECT_ID');
    }
}
