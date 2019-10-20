<?php

require __DIR__ . '/vendor/autoload.php';

$accounts = \app\models\db\Account::findAll();
$tmpAcc = $accounts;
$accounts = [];
foreach ($tmpAcc as $account) {
    $accounts[\strtolower($account->number)] = $account;
}

$jsonRpc = new \app\services\InfuraJsonRpc();
if (!$accounts) {
    if (\app\containers\App::isDebug()) {
        error_log("no accounts to track");
    }
    exit(0);
}

$loop = \React\EventLoop\Factory::create();
$connector = new \Ratchet\Client\Connector($loop);

$connector(\app\services\InfuraWss::getApiUrl())
    ->then(function(Ratchet\Client\WebSocket $conn) use ($accounts) {
        $numbers = array_column($accounts, \app\models\db\Account::FIELD_NUMBER);
        $msg = \app\services\InfuraWss::getEthSubscribeMessage($numbers);
        $conn->send($msg);
        if (\app\containers\App::isDebug()) {
            error_log("sent a subscription msg: $msg");
        }
        $conn->on('message', function($msg) use ($conn, $accounts) {
            echo "Received: {$msg}\n";
            $msg = \json_decode($msg);
            if ($msg->method === 'eth_subscription') {
                $msg->params->result->blockNumber;
                $msg->params->result->transactionHash;
                $t = new \app\models\db\TransactionQueue();
                $t->hash = $msg->params->result->transactionHash;
                $t->accountId = $accounts[\strtolower($msg->params->result->address)]->id;
                $t->save();
                if (\app\containers\App::isDebug()) {
                    error_log("{$msg->params->result->address} saved to transactionQueue.id = $t->id");
                }
                unset($t);
            }
        });

        $conn->on('close', function($code = null, $reason = null) {
            echo "Connection closed ({$code} - {$reason})\n";
        });
    }, function(\Exception $e) use ($loop) {
        echo "Could not connect: {$e->getMessage()}\n";
        $loop->stop();
    });

$loop->run();
