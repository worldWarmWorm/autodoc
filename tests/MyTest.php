<?php

namespace ApiAutodoc\Tests;

use ApiAutodoc\Examples\MyController;
use PHPUnit\Framework\TestCase;

class MyTest extends TestCase
{
    public function testMyTest(): void
    {
        $controller = new MyController();

        self::assertTrue(file_exists('documentation.json'));
        self::assertTrue(file_exists('documentation.yaml'));
    }
}