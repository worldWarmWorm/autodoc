<?php

declare(strict_types=1);

namespace ApiAutodoc\Generators;

interface DocumentationGeneratorInterface
{
    public function generate(string $title, string $file = 'documentation'): void;
}