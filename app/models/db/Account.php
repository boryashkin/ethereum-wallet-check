<?php
namespace app\models\db;

use app\dataTypes\Wei;
use app\services\InfuraJsonRpc;

class Account extends DbRecord
{
    public const FIELD_NUMBER = 'number';
    public $id;
    public $number;
    private $balanceWei;

    public static function getTable(): string
    {
        return 'account';
    }

    public function getBalance():? Wei
    {
        if ($this->balanceWei === null) {
            $j = new InfuraJsonRpc();
            $this->balanceWei = $j->ethGetBalance($this->number);
            unset($j);
        }

        return $this->balanceWei;
    }
}
