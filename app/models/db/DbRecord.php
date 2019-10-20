<?php
namespace app\models\db;

use app\containers\App;

abstract class DbRecord
{
    abstract static function getTable(): string;

    /**
     * @param int $id
     * @return static|null
     */
    public static function findOne(int $id)
    {
        $connection = App::getInstance()->getDbConnection();
        $table = static::getTable();
        $stmt = $connection->prepare(("SELECT * FROM $table WHERE id = :id"));
        $stmt->bindValue(':id', $id);
        $record = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$record) {
            return null;
        }

        $model = new static();
        foreach ($record as $property => $value) {
            if (property_exists($model, $property)) {
                $model->$property = $value;
            }
        }

        return $model;
    }

    /**
     * @param array $andWhere ['field' => 'value']
     * @param array $options ['sort' => SORT_ASC, 'limit' => 10, 'offset' => 10]
     * @return static[]
     */
    public static function findAll(array $andWhere = null, array $options = null)
    {
        $connection = App::getInstance()->getDbConnection();
        $table = static::getTable();
        $whereClause = '';
        if ($andWhere) {
            $whereClause = 'WHERE';
            foreach ($andWhere as $property => $value) {
                $whereClause .= " $property = :$property AND";
            }
            $whereClause = substr($whereClause, 0, -4);
        }
        $optionsClause = '';
        if ($options && $options['sort']) {
            $optionsClause .= ' ORDER BY id ' . ($options['sort'] === SORT_ASC ? 'asc' : 'desc');
        }
        if ($options && $options['limit']) {
            $optionsClause .= ' LIMIT ' . $options['limit'];
            if ($options['offset']) {
                $optionsClause .= ' OFFSET ' . $options['offset'];
            }
        }
        $stmt = $connection->prepare(("SELECT * FROM $table $whereClause $optionsClause"));
        if ($andWhere) {
            foreach ($andWhere as $property => $value) {
                $stmt->bindValue(':' . $property, $value);
            }
        }
        $stmt->execute();
        if ($andWhere) {
            $andWherePlaceholders = [];
            foreach ($andWhere as $property => $value) {
                $andWherePlaceholders[':' . $property] = $value;
            }
            if (App::isDebug()) {
                error_log(strtr($stmt->queryString, $andWherePlaceholders));
            }
        } else {
            error_log($stmt->queryString);
        }
        $models = [];
        while ($record = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (App::isDebug()) {
                error_log("fetched: " . print_r($record, true));
            }
            $model = new static();
            foreach ($record as $property => $value) {
                error_log('property_exists( ' . (get_class($model)) . ",$property) = " . property_exists($model, $property));
                if (property_exists($model, $property)) {
                    $model->$property = $value;
                }
            }
            $models[] = $model;
        }

        return $models;
    }
}
