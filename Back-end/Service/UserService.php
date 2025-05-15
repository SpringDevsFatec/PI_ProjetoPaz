<?php
namespace App\Backend\Service;

use App\Backend\Model\User;
use App\Backend\Repository\UserRepository;

use DateTime;
use DomainException;
use InvalidArgumentException;

class UserService {
    
    private UserRepository $repository;

    public function __construct(
        UserRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    public function getById(int $id): ?array
    {
        return $this->repository->find($id);
    }

    public function getUserByEmail(string $email): bool
    {
        if($this->repository->findUserByEmail($email)){
            return true;
        } else {
            return false;
        }
    }

    public function createUser(array $data): User
    {
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            throw new InvalidArgumentException("Dados incompletos.");       
        }


        $user = new User(
            name: (string)$data['name'],
            email: (string)$data['email'],
            password: (string)$data['password'],
            id: null,
            createdAt: new DateTime(),
            updatedAt: new DateTime()
        );

        $userId = $this->repository->save($user);
        $user->setId($userId);

        return $user;
    }

    public function updateUser(int $userId): User
    {
        $existingUser = $this->repository->find($userId);
        if (!$existingUser) {
            throw new DomainException("Usuario não encontrado.");
        }
        
        $user = new User(
            name: (string)$existingUser['name'],
            email: (string)$existingUser['email'],
            password: (string)$existingUser['password'],
            id: (int)$existingUser['id'],
            createdAt: new DateTime($existingUser['created_at']),
            updatedAt: new DateTime()
        );

        $this->repository->update($user);
        
        return $user;
    }

    public function updatePassord(int $userId): User
    {

        $existingUser = $this->repository->find($userId);
        if (!$existingUser) {
            throw new DomainException("Usuario não encontrado.");
        }
        
        $user = new User(
            name: (string)$existingUser['name'],
            email: (string)$existingUser['email'],
            password: (string)$existingUser['password'],
            id: (int)$existingUser['id'],
            createdAt: new DateTime($existingUser['created_at']),
            updatedAt: new DateTime()
        );

        $this->repository->updatePassword($user);
        
        return $user;
    }

    public function deleteUser($id) {

        $user = $this->repository->find($id);
        if (!$user) {
            throw new DomainException("Usuario não encontrado");
        }
        
        if (!$this->repository->delete($id)) {
            throw new DomainException("Falha ao excluir usuario");
        } 
    }
}