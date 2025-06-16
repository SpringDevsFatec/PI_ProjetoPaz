<?php
// src/Backend/Config/AWS.php
namespace App\Backend\Config;

use App\Backend\Utils\LoadEnv;

class AWSAuth {
    public static string $REGION;
    public static string $VERSION;
    public static string $ACCESS_KEY;
    public static string $SECRET_KEY;
    public static string $BUCKET_PRODUCT;
    public static string $BUCKET_SALE;

    public static function loadFromEnv(): void {
        LoadEnv::loadEnvIntoFiles();

        $required = [
            'AWS_REGION', 'AWS_VERSION', 'AWS_ACCESS_KEY_ID',
            'AWS_SECRET_ACCESS_KEY', 'AWS_BUCKET_PRODUCT', 'AWS_BUCKET_SALE'
        ];

        foreach ($required as $envKey) {
            if (empty($_ENV[$envKey])) {
                throw new \Exception("Variável de ambiente {$envKey} não está definida.");
            }
        }

        self::$REGION         = $_ENV['AWS_REGION'];
        self::$VERSION        = $_ENV['AWS_VERSION'];
        self::$ACCESS_KEY     = $_ENV['AWS_ACCESS_KEY_ID'];
        self::$SECRET_KEY     = $_ENV['AWS_SECRET_ACCESS_KEY'];
        self::$BUCKET_PRODUCT = $_ENV['AWS_BUCKET_PRODUCT'];
        self::$BUCKET_SALE    = $_ENV['AWS_BUCKET_SALE'];
    }
}