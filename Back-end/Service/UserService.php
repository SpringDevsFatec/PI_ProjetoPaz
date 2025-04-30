<?php
namespace App\Backend\Service;

use App\Backend\Model\User;
use App\Backend\Repository\UserRepository;

use Exception;
use DateTime;

class UserService {
    
    private $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    public function readAll() {
        return $this->repository->getAllUsers();
    }

    public function readById($id) {
        return $this->repository->getUserById($id);
    }

    public function login($data) {
        if($this->repository->getUserByEmail($data->email)){
            return true;
        } else {
            return false;
        }
    }

    public function create($data) {
        $user = new User();
        $user->setName($data->name);
        $user->setEmail($data->email);
        $user->setPassword($data->password);
        $user->setDateCreate(new DateTime());

        if ($this->repository->insertUser($user)) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id, $data) {
        $user = new User();
        $user->setId($id);
        $user->setName($data->name ?? $user->getName());
        $user->setEmail($data->email ?? $user->getEmail());

        if ($this->repository->updateUser($user)) {
            return true;
        } else {
            return false;
        }
    }

    public function updatePassord($id, $data) {
        $user = new User();
        $user->setId($id);
        $user->setPassword($data->passord);

        if ($this->repository->updatePassword($user)) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id) {
        if ($this->repository->deleteUser($id)) {
            return true;
        } else {
            return false;
        }
    }
}