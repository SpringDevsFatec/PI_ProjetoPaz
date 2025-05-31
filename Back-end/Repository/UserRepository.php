<?php
namespace App\Backend\Repository;

use PDO;
use App\Backend\Config\Database;
use App\Backend\Model\UserModel;

class UserRepository {

    private $conn;
    private $table = 'user';
    //private $tableLog = '';

    public function __construct() {
        $this->conn = Database::getInstance();
    }

   public function beginTransaction() {
    if (!$this->conn->inTransaction()) {
        $this->conn->beginTransaction();
    }
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
        $userRepo = $stmt->fetchAll(PDO::FETCH_ASSOC);
       if ($stmt->rowCount() > 0) {
                return ['status' => true,'user' => $userRepo];
            }else {
                return ['status' => false, 'user' => null];
            }
    }
    
    //view requests by id
    public function getContentId($id) {
        $query = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

         if ($stmt->rowCount() > 0) {
                $userRepo = $stmt->fetch(PDO::FETCH_ASSOC);
                return ['status' => true,'user' => $userRepo];
            }else {
                return ['status' => false, 'user' => null];
            }
        }


    //view requests by login
    public function verifyLogin($email, $password ) {
       // var_dump($email, $password);die;
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
                $user->setName($userRepo['name']);
                $user->setEmail($userRepo['email']);
                $user->setPassword($userRepo['password']);
                $user->setCreatedAt($userRepo['created_at']);
            
                return ['status' => true,'user' => $userRepo];

            }else {
                // Password does not match
                    return ['status' => false, 'user' => 'false'];
                }
    }else {
            return ['status' => false, 'user' => null];
    }
    }

    // Create a new user
    public function createUser(UserModel $user) {
        $name = $user->getName(); 
        $email = $user->getEmail();
        $password = $user->getPassword(); // Password is already hashed in the model
    
        $query = "INSERT INTO " . $this->table . " (name, email, password) VALUES (:name, :email, :password)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":password", $password, PDO::PARAM_STR);
        $stmt->execute();
        // Check if the user was created successfully
        if ($stmt->rowCount() > 0) {
            $user->setId($this->conn->lastInsertId());
            return ['status' => true, 'user' => $user];
        } else {
            return ['status' => false, 'user' => null];
        }
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
        $stmt->bindParam(":password", $password, PDO::PARAM_STR);
        $stmt->execute();

        // Check if the user was updated successfully
        if ($stmt->rowCount() > 0) {
            return ['status' => true, 'user' => $user];
        } else {
            return ['status' => false, 'user' => null];
        }
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
        if ($stmt->rowCount() > 0) {
            $userRepo = $stmt->fetch(PDO::FETCH_ASSOC);
            return ['status' => false, 'user' => $userRepo]; // Return the number of users with the same email
        } else {
            return ['status' => true, 'user' => null]; // No users found with the same email
        }
    }
}