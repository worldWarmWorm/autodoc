<?php

declare(strict_types=1);

namespace ApiAutodoc\Autodoc;

use ReflectionMethod;

interface AutodocInterface
{
    public function process(ReflectionMethod $endpoint, string $title, string $typeName, array $properties,): array;

    public function save(string $fileName = 'autodoc'): void;
}