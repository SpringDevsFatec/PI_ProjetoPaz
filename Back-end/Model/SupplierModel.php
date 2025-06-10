<?php

namespace App\Backend\Model;

use PDO;
use JsonSerializable;

class SupplierModel implements JsonSerializable {
    private $id;
    private $name;
    private $location;
    private $created_at;

    public function __construct($id = null, $name = null, $location = null, $created_at = null) {
        $this->id = $id;
        $this->name = $name;
        $this->location = $location;
        $this->created_at = $created_at;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getLocation(): ?string {
        return $this->location;
    }

    public function setLocation(string $location): void {
        $this->location = $location;
    }

    public function getCreatedAt(): ?string {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void {
        $this->created_at = $created_at;
    }
    
    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'created_at' => $this->created_at,
        ];
    }
}