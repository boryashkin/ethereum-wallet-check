<?php
namespace app\models\infura;

class Block
{
    private $number;
    private $transactions;

    public $hash;
    public $parentHash;
    public $timestamp;

    public function __construct($number)
    {
        $this->number = $number;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getDecNumber()
    {
        return hexdec($this->number);
    }

    public function getTransactions()
    {
        if (!$this->transactions) {
            $this->transactions = [];
        }

        return $this->transactions;
    }
}
