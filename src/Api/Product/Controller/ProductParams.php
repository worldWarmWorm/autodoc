<?php

declare(strict_types=1);

namespace Autodoc\Api\Product\Controller;

use Autodoc\Autodoc\Params\Params;

/**
 * @template TKey of array-key
 * @template TValue of null|int|string|array
 *
 * @extends Params<TKey, TValue>
 */
class ProductParams extends Params
{
    /**
     * Unique ids of items
     *
     * @var array<int, int>
     */
    public array $ids = [];

    /**
     * Category of items
     */
    public string $category = '';

    /**
     * Count limit of items
     */
    public int $limit = 0;

    /**
     * Offset of items
     */
    public int $offset = 0;
}