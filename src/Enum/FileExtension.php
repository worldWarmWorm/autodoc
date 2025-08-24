<?php

declare(strict_types=1);

namespace Autodoc\Enum;

enum FileExtension: string
{
    case JSON = 'json';
    case YAML = 'yaml';
}
