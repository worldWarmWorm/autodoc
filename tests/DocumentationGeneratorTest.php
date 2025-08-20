<?php

namespace ApiAutodoc\Tests;

use ApiAutodoc\Examples\MyController;
use PHPUnit\Framework\TestCase;

final class DocumentationGeneratorTest extends TestCase
{
    public function testGenerationOfDocumentationFiles(): void
    {
        $controller = new MyController();

        self::assertTrue(file_exists('documentation.json'));
        self::assertTrue(file_exists('documentation.yaml'));
    }
}