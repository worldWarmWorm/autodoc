<?php

declare(strict_types=1);

namespace Autodoc\Tests\Endpoints\Product;

use Autodoc\{
    JsonAutodoc,
    YamlAutodoc};
use Autodoc\EndpointInterface;
use Autodoc\Exceptions\AutodocException;
use Throwable;

final class ProductController implements EndpointInterface
{
    /**
     * @template TKey of array-key
     * @template TValue of null|int|string|array
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