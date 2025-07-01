<?php
namespace App\Backend\Utils;
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv;

class LoadEnv {

public static function loadEnvIntoFiles(): void
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
    }

}