<?php
namespace App\Backend\Config;

use App\Backend\Utils\LoadEnv;

class Token {
    public static string $SECRET_KEY;
    public static  $TOKEN_EXPIRATION;

    public static function Get_ENV()
    {
        // Carrega as variáveis do .env para o $_ENV
        LoadEnv::loadEnvIntoFiles();

        // Agora faz a verificação depois de carregar
        if (empty($_ENV['TOKEN_SECRET_KEY']) || empty($_ENV['TOKEN_EXPIRATION_TIME'])) {
            throw new \Exception("Variáveis de ambiente TOKEN_SECRET_KEY ou TOKEN_EXPIRATION_TIME não estão definidas.");
        }

        // Atribui os valores às propriedades estáticas
        self::$SECRET_KEY = $_ENV['TOKEN_SECRET_KEY'];
        self::$TOKEN_EXPIRATION = $_ENV['TOKEN_EXPIRATION_TIME'];
    }

}