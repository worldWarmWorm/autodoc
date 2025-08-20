<?php

declare(strict_types=1);

namespace ApiAutodoc\Examples;

use ApiAutodoc\Params\Params;

class ItemsParams extends Params
{
    /**
     * Unique ids of items
     */
    public readonly ?array $ids;

    /**
     * Category of items
     */
    public readonly string $category;

    /**
     * Count limit of items
     */
    public readonly int $limit;

    /**
     * Offset of items
     */
    public readonly int $offset;
}