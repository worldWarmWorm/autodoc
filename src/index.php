<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Autodoc\Api\Product\Controller\ProductController;

$productController = new ProductController(__DIR__ . '/Api/Product/Controller/autodoc');

$fileName = __DIR__ . '/Api/Product/View/index.php';
$content = file_get_contents($fileName);

echo $content;