<?php

declare(strict_types=1);

namespace Autodoc\Tests;

use Autodoc\Endpoints\Product\ProductParams;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ParamsTest extends TestCase
{
    /**
     * @param array<mixed> $paramsInput
     * @param array<mixed> $paramsExpected
     */
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
            [null => '', 'ids' => [1, 2], 'category' => 'Category 1', 'limit' => null, 'offset' => null],
            ['ids' => [1, 2], 'category' => 'Category 1', 'limit' => null, 'offset' => null]
        ];
    }
}