<?php

namespace Autodoc\Tests;

use Autodoc\Api\Product\Controller\ProductController;
use Autodoc\Exceptions\AutodocException;
use PHPUnit\Framework\TestCase;

final class EndpointAutodocTest extends TestCase
{
    /**
     * @throws AutodocException
     */
    public function testAutodocProductEndpoints(): void
    {
        $controller = new ProductController(__DIR__ . '/Api/Product/autodoc');

        self::assertTrue(file_exists(__DIR__ . '/Api/Product/autodoc.json'));
    }
}