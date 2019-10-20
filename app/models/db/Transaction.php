<?php
namespace app\models\db;

use app\containers\App;
use app\dataTypes\Wei;
use app\models\infura\Block;
use app\services\InfuraJsonRpc;

class Transaction extends DbRecord
{
    public $id;
    public $hash;
    public $transactionIndex;
    public $blockHash;
    public $blockNumber;
    public $from;
    public $to;
    public $v;
    public $value;
    public $accountId;
    private $saved;
    private $block;

    public static function getTable(): string
    {
        return 'transaction';
    }

    public static function constructFromApi($hash)
    {
        $api = new InfuraJsonRpc();
        $model = new self();
        $info = $api->ethGetTransactionByHash($hash);
        if (!$info) {
            return null;
        }
        foreach ($info as $property => $value) {
            if (property_exists($model, $property)) {
                $model->$property = $value;
            }
        }
        //the model is not saved here

        return $model;
    }

    public function getValueWei()
    {
        return new Wei($this->value);
    }

    public function getBlock()
    {
        if ($this->block === null) {
            $this->block = new Block($this->blockNumber);
        }

        return $this->block;
    }

    public function save()
    {
        if ($this->saved === null) {
            $connection = App::getInstance()->getDbConnection();
            $table = self::getTable();
            $query = <<<SQL
INSERT INTO $table (hash, transactionIndex, blockHash, blockNumber, `from`, `to`, v, value, accountId) VALUES 
(:hash, :transactionIndex, :blockHash, :blockNumber, :from, :to, :v, :value, :accountId)
SQL;
            $stmt = $connection->prepare($query);
            $stmt->bindValue('hash', $this->hash);
            $stmt->bindValue('transactionIndex', $this->transactionIndex);
            $stmt->bindValue('blockHash', $this->blockHash);
            $stmt->bindValue('blockNumber', $this->blockNumber);
            $stmt->bindValue('from', $this->from);
            $stmt->bindValue('to', $this->to);
            $stmt->bindValue('v', $this->v);
            $stmt->bindValue('value', $this->value);
            $stmt->bindValue('accountId', $this->accountId);

            $this->saved = $stmt->execute();
            if ($this->saved) {
                $this->id = $connection->lastInsertId();
            }
            error_log($stmt->queryString);
            error_log('accoundId=' . $this->accountId);
        }

        return $this->saved;
    }
}
