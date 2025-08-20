<?php

namespace ApiAutodoc\Tests;

use ApiAutodoc\Examples\ProductController;
use PHPUnit\Framework\TestCase;

final class DocumentationGeneratorTest extends TestCase
{
    public function testGenerationOfDocumentationFiles(): void
    {
        $controller = new ProductController();

        self::assertTrue(file_exists('documentation.json'));
        self::assertTrue(file_exists('documentation.yaml'));
    }
}