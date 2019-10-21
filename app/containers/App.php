<?php
namespace app\containers;

/**
 * Синглтон "приложения", контейнер с законфигурированными зависимостями
 */
final class App
{
    /** @var \PDO */
    private $dbConnection;

    private static $instance;

    public function getDbConnection()
    {
        return $this->dbConnection;
    }

    public function __wakeup()
    {
        throw new \Exception('Not allowed');
    }

    public static function getInstance(): App
    {
        if (!self::$instance) {
            self::$instance = new App();
            $host = getenv('MYSQL_HOST');
            $db = getenv('MYSQL_DB');
            $user = getenv('MYSQL_USER');
            $pass = getenv('MYSQL_PASSWORD');
            self::$instance->dbConnection = new \PDO("mysql:host=$host;dbname=$db", $user, $pass);
            error_log("mysql:host=$host;dbname=$db" . "; -u $user, -p $pass");
        }

        return self::$instance;
    }

    public static function isDebug()
    {
        return getenv('DEBUG');
    }

    private function __construct() {}

    private function __clone() {}
}
