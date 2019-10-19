<?php

require __DIR__ . '/vendor/autoload.php';

//Using localhost here because we need to connect to websocket just from localhost browser
$app = new Ratchet\App('localhost', 8080, '0.0.0.0');
$app->route('/echo', new Ratchet\Server\EchoServer, array('*'));
$app->run();
