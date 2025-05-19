<?php
namespace App\Backend\Repository;

use PDO;
use App\Backend\Config\Database;
use app\Backend\Model\UserModel;

class UserRepository {

    private $conn;
    private $table = 'user';
    //private $tableLog = '';

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

    // Get all users
    public function getAllUsers() {
        $query = "SELECT * FROM $this->table";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    //view requests by id
    public function getContentId($id) {
        $query = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //view requests by login
    public function verifyLogin($email, $password) {
        $query = "SELECT * FROM $this->table WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        
        // select only the email
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $userRepo = $stmt->fetch(PDO::FETCH_ASSOC);
            // Check if the password matches
            if (password_verify($password, $userRepo['password'])) {
                $user = new UserModel();
                $user->setId($userRepo['id']);
                $user->setName($userRepo['nome']);
                $user->setEmail($userRepo['email']);
                $user->setPassword($userRepo['password']);
                $user->setDateCreate($userRepo['dateCreate']);
                $user->setUpdatedAt($userRepo['updatedAt']);

                return $userRepo;

            }else {
                // Password does not match
                return [false,'message' => 'Senha incorreta'];
            }
        }
        return null;
    }

    // Create a new user
    public function createUser(UserModel $user) {
        $name = $user->getName(); 
        $email = $user->getEmail();
        $password = $user->getPassword(); // Password is already hashed in the model
    
        $query = "INSERT INTO $this->table (name, email, password) VALUES (:name, :email, :password)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":password", password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update user
    public function updateUser(UserModel $user) {
        $id = $user->getId();   
        $name = $user->getName();
        $email = $user->getEmail();
        $password = $user->getPassword(); // Password is already hashed in the model

        $query = "UPDATE $this->table SET name = :name, email = :email, password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":password", password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update the user password
    public function updateUserPassword($id, $password) {
        $query = "UPDATE $this->table SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":password", password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Delete user
    public function deleteUser($id) {
        $query = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function userExists(UserModel $user) {
        $email = $user->getEmail();

        $query = "SELECT * FROM $this->table WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}