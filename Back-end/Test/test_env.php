<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

echo 'Nome do projeto: ' . $_ENV['AWS_NAME'] . PHP_EOL;
echo 'Access Key: ' . $_ENV['AWS_ACCESS_KEY_ID'] . PHP_EOL;
echo 'Secret Key: ' . $_ENV['AWS_SECRET_ACCESS_KEY'] . PHP_EOL;
echo 'Região: ' . $_ENV['AWS_REGION'] . PHP_EOL;