<?php 

namespace App\Backend\Repository;

use App\Backend\Model\User;
use App\Backend\Config\Database;
use PDO;
use PDOException;

class UserRepository {
    private PDO $conn;
    private string $table = 'users';
    private $tableLog = '';

    public function __construct(PDO $conn = null)
    {
        $this->conn = $conn ?: Database::getInstance();
    }

    public function findUserByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAll() {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function save(User $user): int
    {
        $query = "INSERT INTO {$this->table} 
                  (name, email, password, created_at, updated_at) 
                  VALUES (:name, :email, :password, :created_at, :updated_at)";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'created_at' => $user->getCreatedAt()?->format('Y-m-d H:i:s'),
                'updated_at' => $user->getUpdatedAt()?->format('Y-m-d H:i:s'),
            ]);
            
            $userId = $this->conn->lastInsertId();
            return $userId;

        } catch (PDOException $e) {
            throw new PDOException("Erro ao criar usuario: " . $e->getMessage());
        }
    }

    public function update(User $user): bool
    {
        
        $query = "UPDATE {$this->table} 
                  SET name = :name, 
                      email = :email,
                      updated_at = :updated_at
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':id' => $user->getId(),
                ':name' => $user->getName(),
                ':email' => $user->getEmail(),
                ':updated_at' => (new \DateTime)->format('Y-m-d H:i:s'),
            ]);

        } catch (PDOException $e) {
            throw new PDOException("Erro ao atualizar usuario: " . $e->getMessage());
        }
    }

    public function updatePassword(User $user): bool
    {
        $query = "UPDATE {$this->table} 
                  SET password = :password,
                      updated_at = :updated_at
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':id' => $user->getId(),
                ':passoword' => $user->getPassword(),
                ':updated_at' => (new \DateTime)->format('Y-m-d H:i:s'),
            ]);

        } catch (PDOException $e) {
            throw new PDOException("Erro ao atualizar senha: " . $e->getMessage());
        }
    }

    public function delete($id): bool
    {
        try {
            $this->conn->beginTransaction();

            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $id]);
            
            $this->conn->commit();
            return true;
        
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw new PDOException("Erro ao deletar usuario: " . $e->getMessage());
        } 
    }
}