<?php
namespace App\Backend;
use App\Backend\Routers\Router;
use App\Backend\Libs\HttpHeader;
use App\Backend\Routers\Rotas;
use App\Backend\Config\Container;

require_once './vendor/autoload.php';

HttpHeader::setDefaultHeaders();
$container = new Container();
Router::setup($container);

// Obtém o método e URI da requisição
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Resolve a rota
Router::resolve(Rotas::fastRotas(), $method, $uri);

/*
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$arrayRotas = Rotas::fastRotas();

Router::resolve($arrayRotas, $method, $uri);
*/