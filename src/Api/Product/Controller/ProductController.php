<?php

declare(strict_types=1);

namespace Autodoc\Api\Product\Controller;

use Autodoc\Autodoc\Exceptions\AutodocException;
use Autodoc\Autodoc\{EndpointInterface, JsonAutodoc, YamlAutodoc};

final readonly class ProductController implements EndpointInterface
{
    /**
     * @template TKey of array-key
     * @template TValue of null|int|string|array
     *
     * @param ProductParams<TKey, TValue> $params
     * @autodocDescription Method for getting products data
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
     * @throws AutodocException
     */
    public function __construct(string $fileName)
    {
        new JsonAutodoc($this)->generate("The documentation of " . self::class . " endpoints", $fileName);
        new YamlAutodoc($this)->generate("The documentation of " . self::class . " endpoints", $fileName);
    }
}
