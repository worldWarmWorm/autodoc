<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Autodoc\Api\Product\Controller\ProductController;

$productController = new ProductController(__DIR__ . '/Api/Product/Controller/autodoc');

$files = [
    __DIR__ . '/Api/Product/View/autodoc.php',
];



foreach ($files as $file) {
    ob_start();
    include_once($file);
    $file = ob_get_clean();
    echo $file;
}
