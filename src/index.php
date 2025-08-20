<?php

use generators\{JsonDocumentation, YamlDocumentation};

interface EndpointInterface {}

class Controller implements EndpointInterface
{
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

