<?php

namespace App\Backend\Model;

use PDO;
use JsonSerializable;

class SupplierModel implements JsonSerializable {
    private $id;
    private $name;
    private $place;
    private $dataCreate;

    public function __construct($id = null, $name = null, $place = null, $dataCreate = null) {
        $this->id = $id;
        $this->name = $name;
        $this->place = $place;
        $this->dataCreate = $dataCreate;
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

    public function getPlace(): ?string {
        return $this->place;
    }

    public function setPlace(string $place): void {
        $this->place = $place;
    }

    public function getDataCreate(): ?string {
        return $this->dataCreate;
    }

    public function setDataCreate(string $dataCreate): void {
        $this->dataCreate = $dataCreate;
    }
    
    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'place' => $this->place,
            'dataCreate' => $this->dataCreate,
        ];
    }
}