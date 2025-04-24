<?php
namespace App\Backend\Repository;

use PDO;
use App\Backend\Config\Database;
use app\Backend\Model\ModelTest;

class RepositoryTest {

    private $conn;
    private $table = '';
    private $tableLog = '';

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    // begin transaction
    public function beginTransaction() {
        $this->conn->beginTransaction();
    }

    // commit transaction
    public function commitTransaction() {
        $this->conn->commit();
    }

    // roll back transaction
    public function rollBackTransaction() {
        $this->conn->rollBack();
    }

    //public functions...

}