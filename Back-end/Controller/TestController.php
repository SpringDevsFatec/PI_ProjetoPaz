<?php
namespace App\Backend\Controller;
use App\Backend\Service\ServiceTest;
use App\Backend\Libs\AuthMiddleware;
use Exception;

class ControllerTest {
    private $service;

    public function __construct() {
        $this->service = new ServiceTest();
    }

    // code Get, Post, Put, Delete in private functions

}