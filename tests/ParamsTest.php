<?php

declare(strict_types=1);

namespace ApiAutodoc\Tests;

use ApiAutodoc\Endpoints\Product\ProductParams;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ParamsTest extends TestCase
{
    #[DataProvider('paramsProvider')]
    public function testParamsConstructor(array $paramsInput, array $paramsExpected): void
    {
        $productParams = new ProductParams($paramsInput);
        self::assertEquals(count($paramsExpected), $productParams->count());

        foreach ($productParams as $key => $productParam) {
            self::assertArrayHasKey($key, $paramsExpected);
            self::assertEquals($paramsExpected[$key], $productParam);
        }
    }

    public static function paramsProvider(): \Generator
    {
        yield [
            [null => '', '' => null, 'ids' => [1, 2], 'category' => 'Category 1', 'limit' => null, 'offset' => null],
            ['ids' => [1, 2], 'category' => 'Category 1', 'limit' => null, 'offset' => null]
        ];
    }
}