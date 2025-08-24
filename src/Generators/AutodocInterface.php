<?php

declare(strict_types=1);

namespace ApiAutodoc\Generators;

use ReflectionMethod;

interface AutodocInterface
{
    public function process(
        ReflectionMethod $endpoint,
        string $title, 
        string $typeName,
        array $properties,
    ): array;

    public function save(string $fileName = 'documentation'): void;
}