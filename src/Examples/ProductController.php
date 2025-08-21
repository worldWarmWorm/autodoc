<?php

namespace ApiAutodoc\Examples;

use ApiAutodoc\Endpoints\EndpointInterface;
use ApiAutodoc\Generators\{ApiAutodocException, JsonDocumentation, YamlDocumentation};
use ReflectionException;
use Throwable;

final class ProductController implements EndpointInterface
{
    /**
     * @throws Throwable|ReflectionException|ApiAutodocException
     */
    public function __construct()
    {
        new JsonDocumentation($this)->generate();
        new YamlDocumentation($this)->generate();
    }

    /**
     * @param ProductParams $params
     * @return array{success: bool, products: string}
     */
    public function getProducts(ProductParams $params): array
    {
        $params = [
            'ids' => [1, 2, 3],
            'category' => 'electronics'
        ];

        $ids = implode(', ', $params['ids']);

        return [
            'success' => true,
            'products' => "You just got products with ids: $ids of category {$params['category']}",
        ];
    }
}