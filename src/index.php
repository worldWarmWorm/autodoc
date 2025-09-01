<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Autodoc\Api\Product\Controller\ProductController;

$productController = new ProductController(__DIR__ . '/Api/Product/Controller/autodoc');
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Autodoc</title>
    <style>
        *, *::after, *::before {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-size: 16px;
            font-family: "Roboto Light", "Roboto Thin", monospace;
        }
        body {
            padding: 20px;
            background-color: black;
            color: white;
        }
        header {
            padding-bottom: 40px;
        }
        footer {
            padding-top: 40px;
        }
        code {
            color: #01c601;
        }
        h1 {
            font-size: 24px;
        }
        a[href] {
            color: red;
        }
    </style>
</head>
<body>
<header>
    <h1>Documentation</h1>
</header>
<main>
    <?php
        $files = [
            '/Api/Product/Controller/autodoc.json',
        ];

        foreach ($files as $file) {
            echo '<pre><code>' . file_get_contents(__DIR__ . $file) . '</pre></code>';
        }
    ?>
</main>
<footer>
    <p>Build with <a href="https://github.com/worldWarmWorm/autodoc" target="_blank">autodoc</a></p>
</footer>
</body>
</html>
