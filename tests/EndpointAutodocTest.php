<?php

namespace Autodoc\Tests;

use Autodoc\Tests\Endpoints\Product\ProductController;
use PHPUnit\Framework\TestCase;

final class EndpointAutodocTest extends TestCase
{
    public function testAutodocProductEndpoints(): void
    {
        $controller = new ProductController();

        self::assertTrue(file_exists('autodoc.json'));
        self::assertTrue(file_exists('autodoc.yaml'));
    }
}