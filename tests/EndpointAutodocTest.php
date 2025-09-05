<?php

namespace Autodoc\Tests;

use Autodoc\Api\Product\Controller\ProductController;
use Autodoc\Autodoc\Exceptions\AutodocException;
use PHPUnit\Framework\TestCase;

final class EndpointAutodocTest extends TestCase
{
    /**
     * @throws AutodocException
     */
    public function testAutodocProductEndpoints(): void
    {
        new ProductController(__DIR__ . '/autodoc');

        self::assertTrue(file_exists(__DIR__ . '/autodoc.json'));
        self::assertTrue(file_exists(__DIR__ . '/autodoc.yaml'));
    }
}
