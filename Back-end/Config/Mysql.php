<?php
namespace App\Backend\Config;

use App\Backend\Utils\LoadEnv;

class MySql {
    public static string $HOST;
    public static string $DB_NAME;
    public static string $USERNAME;
    public static string $PASSWORD;
    public static string $PORT;
    public static string $CHARSET;

    public static function loadFromEnv(): void {
        // Load environment variables from .env file into $_ENV
        LoadEnv::loadEnvIntoFiles();

        // Verify if the required environment variables are set
        $required = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_PORT', 'DB_CHARSET'];
        foreach ($required as $envKey) {
            if (empty($_ENV[$envKey])) {
                throw new \Exception("Variável de ambiente {$envKey} não está definida.");
            }
        }

        // Assign the environment variables to the static properties
        self::$HOST     = $_ENV['DB_HOST'];
        self::$DB_NAME  = $_ENV['DB_NAME'];
        self::$USERNAME = $_ENV['DB_USER'];
        self::$PASSWORD = $_ENV['DB_PASSWORD'];
        self::$PORT     = $_ENV['DB_PORT'];
        self::$CHARSET  = $_ENV['DB_CHARSET'];
    }
}
