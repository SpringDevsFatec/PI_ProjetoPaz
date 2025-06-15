<?php
namespace App\Backend\Config;

require_once __DIR__ . '/../vendor/autoload.php';

use App\Backend\Utils\LoadEnv;
class AWSAuth {
    
    public static function getConfigAWS(): array
    {
        LoadEnv::loadEnvIntoFiles();

        return [
            'region' => $_ENV['AWS_REGION'] ,
            'version' => $_ENV['AWS_VERSION'] ,
            'Buckets' => [
                'Products' =>[
                    'bucket_name' => $_ENV['AWS_NAME'] ,
                    'access_key' => $_ENV['AWS_ACCESS_KEY_ID'] ,
                    'secret_key' => $_ENV['AWS_SECRET_ACCESS_KEY'] ,
                ],
                'Sales' =>[
                    'bucket_name' => $_ENV['AWS_NAME'] ,
                    'access_key' => $_ENV['AWS_ACCESS_KEY_ID'] ,
                    'secret_key' => $_ENV['AWS_SECRET_ACCESS_KEY'] ,
                ],

            ],
        ];
    }
}
/* testando a classe AWSAuth
var_dump( AWSAuth::getConfigAWS());*/
