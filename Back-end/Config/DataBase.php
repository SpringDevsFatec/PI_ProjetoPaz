<?php
namespace App\Backend\Config;

use PDO;
use PDOException;
use Exception;

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            // Load environment variables from .env file into $_ENV
            // Yes myself on future, if would you want to change the model database, you wiil need to had pacience to create a new class based on Mysql, and change the all things that have referecing him here. good locky!(yeah i believe that you english will be better when you read this hehe) 
            MySql::loadFromEnv();

            $dsn = "mysql:host=" . MySql::$HOST . 
                   ";port=" . MySql::$PORT . 
                   ";dbname=" . MySql::$DB_NAME . 
                   ";charset=" . MySql::$CHARSET;

            $this->conn = new PDO($dsn, MySql::$USERNAME, MySql::$PASSWORD, [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
        } catch (Exception $exception) {
            echo "Erro de conexão2: " . $exception->getMessage();
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->conn;
    }
}
