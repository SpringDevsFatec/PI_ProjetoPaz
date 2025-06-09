<?php

namespace App\Backend\Repository;

use PDO;
use PDOException;
use Exception;

class SupplierRepository {
    private $db;
    private $table = 'supplier';

    public function __construct(PDO $dbConnection) {
        $this->db = $dbConnection;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function find(int $id) {
        try {
            $stmt = $this->db->prepare("SELECT id, name, location, created_at FROM $this->table WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar fornecedor por ID: " . $e->getMessage());
        }
    }

    public function findAll(): array {
        try {
            $stmt = $this->db->prepare("SELECT id, name, location, created_at FROM $this->table");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar todos os fornecedores: " . $e->getMessage());
        }
    }

    public function create(object $data) {
        try {
            $stmt = $this->db->prepare("INSERT INTO $this->table (name, location) VALUES (:name, :location)");
            $stmt->bindParam(':name', $data->name, PDO::PARAM_STR);
            $stmt->bindParam(':location', $data->location, PDO::PARAM_STR);

            if ($stmt->execute()) {
                return (int)$this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Erro ao criar fornecedor: " . $e->getMessage());
        }
    }

    public function update(object $data): bool {
        try {
            $setClauses = [];
            $params = ['id' => $data->id];

            if (isset($data->name)) {
                $setClauses[] = "name = :name";
                $params[':name'] = $data->name;
            }
            if (isset($data->location)) {
                $setClauses[] = "location = :location";
                $params[':location'] = $data->location;
            }

            if (empty($setClauses)) {
                return true;
            }

            $sql = "UPDATE $this->table SET " . implode(', ', $setClauses) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                if ($key === 'id') {
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value, PDO::PARAM_STR);
                }
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar fornecedor: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM $this->table WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erro ao excluir fornecedor: " . $e->getMessage());
        }
    }

    public function findByName(string $name) {
        try {
            $stmt = $this->db->prepare("SELECT id, name, location, created_at FROM $this->table WHERE name = :name");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar fornecedor por nome: " . $e->getMessage());
        }
    }
}