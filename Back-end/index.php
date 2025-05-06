<?php
namespace App\Backend;
use App\Backend\Routers\Router;
use App\Backend\Libs\HttpHeader;
use App\Backend\Routers\Rotas;

require_once './vendor/autoload.php';

HttpHeader::setDefaultHeaders();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$arrayRotas = Rotas::fastRotas();

Router::resolve($arrayRotas, $method, $uri);
