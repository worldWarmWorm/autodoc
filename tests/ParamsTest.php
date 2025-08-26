<?php

declare(strict_types=1);

namespace Autodoc\Tests;

use Autodoc\Exceptions\AutodocException;
use Autodoc\Tests\Endpoints\Product\ProductParams;
use PHPUnit\Framework\TestCase;

final class ParamsTest extends TestCase
{
    /**
     * @template TKey of array-key
     * @template TValue of null|int|string|array
     *
     * @param array<TKey|null, TValue> $paramsInput
     * @param array<TKey, TValue> $paramsExpected
     *
     * @dataProvider paramsProvider
     * @throws AutodocException
     */
    public function testParamsConstructor(array $paramsInput, array $paramsExpected): void
    {
        /**
         * @template TKey of array-key
         * @template TValue of null|int|string|array
         *
         * @var ProductParams<TKey, TValue> $productParams
         */
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
            [null => '', 'ids' => [1, 2], 'category' => 'Category 1', 'limit' => null, 'offset' => null],
            ['ids' => [1, 2], 'category' => 'Category 1', 'limit' => null, 'offset' => null]
        ];
    }
}