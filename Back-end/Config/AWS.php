<?php
namespace App\Backend\Config;

use App\Backend\Model\Product;

class AWSAuth {
    const REGION = 'us-east-1';
    const VERSION = 'latest';
    const BUCKET = [
        "Product" => [
            "NAME" => "project-paz-product",
            "ACESS_KEY_ID" => "AKIAS2VS4K2OFFYLV6JA/",
            "SECRET_ACCESS_KEY" => "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"
        ],
        "Sale" => [
            "NAME" => "project-paz-sale",
            "ACESS_KEY_ID" => "AKIAS2VS4K2OFFYLV6JA/",
            "SECRET_ACCESS_KEY" => "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"
        ],
    ];
    
}