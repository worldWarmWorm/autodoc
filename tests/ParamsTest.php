<?php

declare(strict_types=1);

namespace Autodoc\Tests;

use Autodoc\Autodoc\Exceptions\AutodocException;
use Autodoc\Api\Product\Controller\ProductParams;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class ParamsTest extends TestCase
{
    /**
     * @template TKey of array-key
     * @template TValue of null|int|string|array
     *
     * @param array<TKey|null, TValue> $paramsInput
     * @param array<TKey, TValue> $paramsExpected
     *
     * @dataProvider inputParamsProvider
     *
     * @throws AutodocException
     */
    public function testInputParams(array $paramsInput, array $paramsExpected): void
    {
        if (in_array(AutodocException::class, $paramsExpected, true)) {
            self::expectException(AutodocException::class);
            self::expectExceptionMessageMatches('/Incorrect type for property/');
        }

        /**
         * @template TKey of array-key
         * @template TValue of null|int|string|array
         *
         * @var ProductParams<TKey, TValue> $productParams
         */
        $productParams = new ProductParams($paramsInput);
        $reflection = new ReflectionClass($productParams);
        $properties = $reflection->getProperties();

        self::assertCount(count($paramsExpected), $properties);

        foreach ($paramsExpected as $propName => $propValue) {
            self::assertObjectHasProperty($propName, $productParams);
            self::assertEquals($productParams->$propName, $propValue);
        }
    }

    public static function inputParamsProvider(): \Generator
    {
        yield [
            [],
            ['ids' => [], 'category' => '', 'limit' => 0, 'offset' => 0]
        ];
        yield [
            ['ids' => [1, 2]],
            ['ids' => [1, 2], 'category' => '', 'limit' => 0, 'offset' => 0]
        ];
        yield [
            [null => '', 'ids' => [1, 2], 'category' => 'Category 1', 'limit' => 100, 'offset' => 0],
            ['ids' => [1, 2], 'category' => 'Category 1', 'limit' => 100, 'offset' => 0],
        ];
        yield [
            ['ids' => ''],
            [AutodocException::class]
        ];
        yield [
            [100 => [1, 2]],
            ['ids' => [], 'category' => '', 'limit' => 0, 'offset' => 0]
        ];
        yield [
            [true => false],
            ['ids' => [], 'category' => '', 'limit' => 0, 'offset' => 0]
        ];
    }
}