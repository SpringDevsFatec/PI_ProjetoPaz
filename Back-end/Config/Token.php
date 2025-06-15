<?php
namespace App\Backend\Config;

use App\Backend\Utils\LoadEnv;

class Token {
    public string $SECRET_KEY;
    public string $TOKEN_EXPIRATION;

    public function __construct()
    {
        LoadEnv::loadEnvIntoFiles();

        $this->SECRET_KEY = $_ENV['TOKEN_SECRET_KEY'];
        $this->TOKEN_EXPIRATION = $_ENV['TOKEN_EXPIRATION_TIME'];
    }
}
