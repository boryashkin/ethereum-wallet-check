<?php
namespace app\models\db;

use app\containers\App;

class TransactionQueue extends DbRecord
{
    public const FIELD_STATUS = 'status';

    public const STATUS_READY = 0;
    public const STATUS_PROGRESS = 1;
    public const STATUS_DONE = 2;
    public const STATUS_FAIL = 3;
    public $id;
    public $hash;
    public $status = self::STATUS_READY;
    public $accountId;
    private $saved;

    public static function getTable(): string
    {
        return 'transaction_queue';
    }

    public function save()
    {
        $connection = App::getInstance()->getDbConnection();
        $table = self::getTable();
        if ($this->id === null) {
            $query = <<<SQL
INSERT INTO $table (hash, status, accountId) VALUES 
(:hash, :status, :accountId)
SQL;
            $stmt = $connection->prepare($query);
            $stmt->bindValue('hash', $this->hash);
            $stmt->bindValue('status', $this->status);
            $stmt->bindValue('accountId', $this->accountId);

            $this->saved = $stmt->execute();
            if ($this->saved) {
                $this->id = $connection->lastInsertId();
            }
        } else {
            $query = <<<SQL
UPDATE $table SET hash = :hash, status = :status, accountId = :accountId WHERE id = :id
SQL;
            $stmt = $connection->prepare($query);
            $stmt->bindValue('id', $this->id);
            $stmt->bindValue('hash', $this->hash);
            $stmt->bindValue('status', $this->status);
            $stmt->bindValue('accountId', $this->accountId);

            $this->saved = $stmt->execute();
        }
        if (App::isDebug()) {
            error_log($stmt->queryString);
            error_log('accoundId=' . $this->accountId);
        }

        return $this->saved;
    }
}
