<?php

use examples\ItemsParams;
use params\EndpointInterface;
use generators\{ApiAutodocException, JsonDocumentation, YamlDocumentation};

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

function init(): void
{
    $controller = new MyController();

    echo "Documentation created";
}

init();

