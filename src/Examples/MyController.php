<?php

namespace ApiAutodoc\Examples;

use ReflectionException;
use Throwable;
use ApiAutodoc\Generators\{ApiAutodocException, JsonDocumentation, YamlDocumentation};

final class MyController implements EndpointInterface
{
    /**
     * @throws Throwable|ReflectionException|ApiAutodocException
     */
    public function __construct()
    {
        new JsonDocumentation($this)->generate();
        new YamlDocumentation($this)->generate();
    }

    public function getItems(ItemsParams $params): array
    {
        return [
            'success' => true,
            'message' => 'Items retrieved',
        ];
    }
}