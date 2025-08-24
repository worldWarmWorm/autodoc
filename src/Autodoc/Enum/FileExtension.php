<?php

declare(strict_types=1);

namespace ApiAutodoc\Autodoc\Enum;

enum FileExtension: string
{
    case JSON = 'json';
    case YAML = 'yaml';
}
