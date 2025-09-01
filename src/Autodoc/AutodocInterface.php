<?php

declare(strict_types=1);

namespace Autodoc\Autodoc;

use ReflectionMethod, ReflectionProperty;

interface AutodocInterface
{
    /**
     * @param array<int, ReflectionProperty> $properties
     * @return array<mixed>
     */
    public function process(ReflectionMethod $endpoint, string $title, string $typeName, array $properties): array;

    public function save(string $fileName): void;
}