<?php

declare(strict_types=1);

namespace Autodoc\Tests\Endpoints\Product;

use Autodoc\Params\Params;

/**
 * @template TKey of array-key
 * @template TValue of null|int|string|array
 *
 * @extends Params<TKey, TValue>
 */
final class ProductParams extends Params
{
    /**
     * Unique ids of items
     *
     * @var array<int, int>
     */
    public array $ids;

    /**
     * Category of items
     */
    public string $category;

    /**
     * Count limit of items
     */
    public ?int $limit = null;

    /**
     * Offset of items
     */
    public ?int $offset = null;
}