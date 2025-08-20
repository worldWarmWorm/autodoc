<?php

declare(strict_types=1);

use ApiAutodoc\Examples\MyController;

require_once '../../vendor/autoload.php';

function init(): void
{
    $controller = new MyController();

    echo "Documentation created";
}

init();

