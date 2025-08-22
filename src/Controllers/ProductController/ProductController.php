<?php

namespace ApiAutodoc\Controllers\ProductController;

use ApiAutodoc\Controllers\EndpointInterface;
use ApiAutodoc\Exceptions\ApiAutodocException;
use ApiAutodoc\Generators\{
    JsonDocumentation,
    YamlDocumentation};
use Throwable;

final class ProductController implements EndpointInterface
{
    /**
     * @template TKey of array-key
     * @template TValue
     *
     * @param ProductParams<TKey, TValue> $params
     *
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

    /**
     * @throws Throwable|ApiAutodocException
     */
    public function __construct()
    {
        $commonTitlePart = "The documentation of #" . self::class . " endpoints";

        (new JsonDocumentation($this))->generate($commonTitlePart, 'doc', 'doc');
        (new YamlDocumentation($this))->generate("# $commonTitlePart \n\n", 'doc', 'doc');
    }
}