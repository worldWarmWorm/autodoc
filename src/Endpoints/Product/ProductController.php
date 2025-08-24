<?php

declare(strict_types=1);

namespace ApiAutodoc\Endpoints\Product;

use ApiAutodoc\Autodoc\{JsonAutodoc, YamlAutodoc};
use ApiAutodoc\Autodoc\Exceptions\AutodocException;
use ApiAutodoc\Endpoints\EndpointInterface;
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
     * @throws AutodocException|Throwable
     */
    public function __construct()
    {
        $title = "The documentation of #" . self::class . " endpoints";

        (new JsonAutodoc($this))->generate($title);
        (new YamlAutodoc($this))->generate($title);
    }
}