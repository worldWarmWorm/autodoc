<?php

declare(strict_types=1);

namespace ApiAutodoc\Examples;

use ApiAutodoc\Params\Params;

class ProductParams extends Params
{
    /**
     * Unique ids of items
     */
    public ?array $ids;

    /**
     * Category of items
     */
    public string $category;

    /**
     * Count limit of items
     */
    public int $limit;

    /**
     * Offset of items
     */
    public int $offset;
}