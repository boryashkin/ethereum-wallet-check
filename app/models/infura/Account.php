<?php
namespace app\models\infura;

class Account
{
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getTransactionsByAccount($account, Block $startBlock, Block $endBlock = null) {
        error_log('Searching for transactions to/from account "' . $account . '" within blocks ' . $startBlock->getNumber() . ' and ' . $endBlock->getNumber());
        $nextBlock = $startBlock;
        while ($nextBlock && $nextBlock->getNumber() !== $endBlock->getNumber()) {
            if ($nextBlock->getTransactions() != null) {
                foreach ($nextBlock->getTransactions() as $transaction) {
                    if ($account == "*" || $account == $transaction->from || $account == $transaction->to) {
                        error_log("  tx hash          : " . $transaction->hash . "\n"
                            . "   nonce           : " . $transaction->nonce . "\n"
                            . "   blockHash       : " . $transaction->blockHash . "\n"
                            . "   blockNumber     : " . $transaction->blockNumber . "\n"
                            . "   transactionIndex: " . $transaction->transactionIndex . "\n"
                            . "   from            : " . $transaction->from . "\n"
                            . "   to              : " . $transaction->to . "\n"
                            . "   value           : " . $transaction->value . "\n"
                            . "   time            : " . $nextBlock->timestamp . " " . (new \DateTime($nextBlock->timestamp * 1000))->format(DATE_ATOM) . "\n"
                            . "   gasPrice        : " . $transaction->gasPrice . "\n"
                            . "   gas             : " . $transaction->gas . "\n"
                            . "   input           : " . $transaction->input);
                    }
                }
            }

            $nextBlock = $nextBlock->getNextBlock();
        }
    }
}
