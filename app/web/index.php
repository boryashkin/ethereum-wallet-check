<?php

require __DIR__ . '/../vendor/autoload.php';

$accounts = \app\models\db\Account::findAll();
$tmpAcc = $accounts;
$accounts = [];
foreach ($tmpAcc as $account) {
    $accounts[$account->number] = $account;
}
/** @var \app\models\db\Transaction[][] $accTransactions */
$accTransactions = [];
foreach ($accounts as $account) {
    $accTransactions[$account->number] = \app\models\db\Transaction::findAll(['accountId' => $account->id], ['limit' => 20, 'offset' => 0, 'sort' => SORT_DESC]);
}
$accLatestTx = $accTransactions ? $accTransactions[0] : null;

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Eth account monitor</title>
    </head>
    <body>
            <div class="container">
                <h3>Latest transactions:</h3>
                <?php foreach ($accTransactions as $accountNum => $transactions) : ?>
                    <div><b>Account: </b><a href="https://etherscan.io/address/<?= $accountNum ?>"><?= $accountNum ?></a></div>
                    <div><b>Balance: </b><?= $accounts[$accountNum]->getBalance()->getEth() ?>eth</div>
                    <table>
                        <tr>
                            <th>Transaction</th>
                            <th>Sum</th>
                            <th>Status</th>
                            <th>Block</th>
                        </tr>
                        <?php foreach ($transactions as $transaction) : ?>
                            <tr>
                                <td><a href="https://etherscan.io/tx/<?= $transaction->hash ?>"><?= $transaction->hash ?></a></td>
                                <td><?= $transaction->getValueWei()->getEth() ?>eth (<?= $transaction->value ?>wei)</td>
                                <td><?= hexdec($transactions[0]->getBlock()->getNumber()) - hexdec($transaction->getBlock()->getNumber()) ?></td>
                                <td>
                                    <a href="https://etherscan.io/block/<?= $transaction->getBlock()->getDecNumber() ?>"><?= $transaction->getBlock()->getNumber() ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endforeach; ?>
            </div>
    </body>
</html>