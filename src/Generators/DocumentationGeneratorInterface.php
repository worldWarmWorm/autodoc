<?php

declare(strict_types=1);

namespace ApiAutodoc\Generators;

use ReflectionMethod;

interface DocumentationGeneratorInterface
{
    public function process(
        string $endpoint, 
        string $title, 
        string $typeName,
        array $properties,
    ): array;

    public function save(string $fileName = 'documentation'): void;
}