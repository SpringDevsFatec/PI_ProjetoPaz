<?php
namespace App\Backend\Service;

use App\Backend\Model\ModelTest;
use App\Backend\Repository\RepositoryTest;
use App\Backend\Libs\AuthMiddleware;
use Exception;


class ServiceTest {
    
    private $repository;

    public function __construct() {
        $this->repository = new RepositoryTest();
    }

    
    //public functions...
}