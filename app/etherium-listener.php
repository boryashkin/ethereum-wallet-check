<?php

require __DIR__ . '/vendor/autoload.php';

$app = \app\containers\App::getInstance();

\Ratchet\Client\connect('ws://eth-server:8080/echo')->then(function(Ratchet\Client\WebSocket $conn) {
    $conn->on('message', function($msg) use ($conn) {
        echo "Received: {$msg}\n";
        $conn->close();
    });

    $conn->send('Hello World!');
}, function (\Exception $e) {
    echo "Could not connect: {$e->getMessage()}\n";
});