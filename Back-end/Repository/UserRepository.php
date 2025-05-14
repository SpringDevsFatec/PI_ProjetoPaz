<?php 

namespace App\Backend\Repository;

use App\Backend\Model\User;
use App\Backend\Config\Database;
use PDO;

class UserRepository {
    private $conn;
    private $table = 'users';
    private $tableLog = '';

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function getAllUsers() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertUser(User $user) {
        $name = $user->getName();
        $email = $user->getEmail();
        $password = $user->getPassword();
        $dateCreate = $user->getDateCreate();

        $query = "INSERT INTO " . $this->table . " (name, email, password, date_create) VALUES (:name, :email, :password, :date_create)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', password_hash($password, PASSWORD_BCRYPT));
        $stmt->bindParam(':date_create', $dateCreate);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateUser(User $user) {
        $id = $user->getId();
        $name = $user->getName();
        $email = $user->getEmail();
        
        $query = "UPDATE " . $this->table . " SET name = :name, email = :email WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updatePassword(User $user) {
        $id = $user->getId();
        $password = $user->getPassword();
        
        $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':password', password_hash($password, PASSWORD_BCRYPT));
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteUser($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}