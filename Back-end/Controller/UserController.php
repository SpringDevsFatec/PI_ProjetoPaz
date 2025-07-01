<?php
namespace App\Backend\Controller;

use App\Backend\Service\UserService;
use App\Backend\Utils\Responses;

class UserController {
    
    private $service;

    // Use the Responses trait 
    use Responses;

    public function __construct() {
        $this->service = new UserService();
    }

    public function login() {
        $data = json_decode(file_get_contents('php://input'));

        if ($result = $this->service->login($data)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
    }

    public function getUserById() {
        if ($result = $this->service->getUserById()) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
    }

    public function getAllUsers() {
        if ($result = $this->service->getAllUsers()) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
    }

    public function createUser() {
        $data = json_decode(file_get_contents('php://input'));

        if ($result = $this->service->createUser($data)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 201);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function updateUser() {
        $data = json_decode(file_get_contents('php://input'));

        if ($result = $this->service->updateUser($data)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function updateUserPassword() {
        $data = json_decode(file_get_contents('php://input'));

        if ($result = $this->service->updateUserPassword($data)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }
}
