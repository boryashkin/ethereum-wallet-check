<?php

require __DIR__ . '/vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

$loop->addPeriodicTimer(1, function () {
    $queue = \app\models\db\TransactionQueue::findAll([
        \app\models\db\TransactionQueue::FIELD_STATUS => \app\models\db\TransactionQueue::STATUS_READY
    ]);
    foreach ($queue as $transactionToHandle) {
        $transactionToHandle->status = \app\models\db\TransactionQueue::STATUS_PROGRESS;
        $transactionToHandle->save();
        $maxLoop = 5;
        do {
            $t = \app\models\db\Transaction::constructFromApi($transactionToHandle->hash);
            if (!$t) {
                usleep(200);
                $t = \app\models\db\Transaction::constructFromApi($transactionToHandle->hash);
            }
        } while (!$t && $maxLoop--);
        if (!$t) {
            $transactionToHandle->status = \app\models\db\TransactionQueue::STATUS_FAIL;
            $transactionToHandle->save();
            error_log("{$transactionToHandle->id} failed");
            continue;
        }
        $t->accountId = $transactionToHandle->accountId;
        $t->save();
        if (\app\containers\App::isDebug()) {
            error_log("{$t->id} transaction saved");
            error_log("{$transactionToHandle->id} done");
        }
    }
});

$loop->run();
