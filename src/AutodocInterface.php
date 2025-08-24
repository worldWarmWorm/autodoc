<?php

declare(strict_types=1);

namespace Autodoc;

use ReflectionMethod, ReflectionProperty;

interface AutodocInterface
{
    /**
     * @param ReflectionMethod $endpoint
     * @param string $title
     * @param string $typeName
     * @param array<int, ReflectionProperty> $properties
     * @return array<mixed>
     */
    public function process(ReflectionMethod $endpoint, string $title, string $typeName, array $properties): array;

    public function save(string $fileName = 'autodoc'): void;
}