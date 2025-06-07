<?php
namespace App\Backend\Controller;

use App\Backend\Service\UserService;
use App\Backend\Utils\Responses;

class UserController {
    private $service;

    public function __construct() {
        $this->service = new UserService();
    }

    // Login method to authenticate users
    public function login() {
        $data = json_decode(file_get_contents('php://input'));

        if($result = $this->service->login($data)) {
            Responses::send($result['status'], $result['message'], $result['content'], 200);
        } else {
            Responses::send($result['status'], $result['message'], $result['content'], 401);
        }
    }

    // Methods to handle user operations
    public function getUserById() {
        if($result = $this->service->getUserById()) {
            Responses::send($result['status'], $result['message'], $result['content'], 200);
        } else {
            Responses::send($result['status'], $result['message'], $result['content'], 401);
        }
    }

    // Method to retrieve all users
    public function getAllUsers() {
        if($result = $this->service->getAllUsers()) {
            Responses::send($result['status'], $result['message'], $result['content'], 200);
        } else {
            Responses::send($result['status'], $result['message'], $result['content'], 401);
        }
    }

    // Method to create a new user
    public function createUser() {
        $data = json_decode(file_get_contents('php://input'));

        if($result = $this->service->createUser($data)) {
            Responses::send($result['status'], $result['message'], $result['content'], 201);
        } else {
            Responses::send($result['status'], $result['message'], $result['content'], 404);
        }
    }

    // Method to update an existing user
    public function updateUser() {
        $data = json_decode(file_get_contents('php://input'));

        if($result = $this->service->updateUser($data)) {
            Responses::send($result['status'], $result['message'], $result['content'], 200);
        } else {
            Responses::send($result['status'], $result['message'], $result['content'], 404);
        }
    }

    // Method to upate a user's password
    public function updateUserPassword() {
        $data = json_decode(file_get_contents('php://input'));

        if($result = $this->service->updateUserPassword($data)) {
            Responses::send($result['status'], $result['message'], $result['content'], 200);
        } else {
            Responses::send($result['status'], $result['message'], $result['content'], 404);
        }
    }
}
