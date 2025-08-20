<?php

declare(strict_types=1);

namespace examples;

use params\Params;

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