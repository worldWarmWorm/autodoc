<?php

declare(strict_types=1);

namespace Autodoc\Autodoc;

use ReflectionMethod;
use ReflectionClass;

interface AutodocInterface
{
    /**
     * @return array<mixed>
     */
    public function process(
        ReflectionClass $reflectionClass,
        ReflectionMethod $endpoint,
        string $title,
        string $typeName
    ): array;

    public function save(string $fileName): void;
}
