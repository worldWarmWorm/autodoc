<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return RectorConfig::configure()
    ->withRules([
        TypedPropertyFromStrictConstructorRector::class
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true
    )
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSets([LevelSetList::UP_TO_PHP_81]);