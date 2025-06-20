<?php
namespace App\Backend\Test;
// public/test.php
require_once '../vendor/autoload.php';
require_once '../App/Backend/Repository/SaleRepository.php';

use App\Backend\Repository\SaleRepository;

$saleRepo = new SaleRepository();
$response = $saleRepo->findAll();
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
