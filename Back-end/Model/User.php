<?php

namespace App\Backend\Model;

use DateTimeInterface;
use DateTime;

class User {
    private ?int $id;
    private string $name;
    private string $email;
    private string $password;
    private ?DateTimeInterface $createdAt;
    private ?DateTimeInterface $updatedAt;
    
    public function __construct(
        ?int $id,
        string $name,
        string $email,
        string $password,
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updatedAt = null
    ) {
        $this->id = $id;
        $this->setName($name);
        $this->setEmail($email);
        $this->setPassword($password);
        $this->createdAt = $createdAt ?? new DateTime();
        $this->updatedAt = $updatedAt ?? new DateTime();
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public function checkPassword($password) {
        return password_verify($password, $this->password);
    }

    public function updateFromArray(array $data): void {
        if (isset($data['name'])) {
            $this->setName($data['name']);
        }

        if (isset($data['email'])) {
            $this->setEmail($data['email']);
        }
    }
    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s')
        ];
    }
}