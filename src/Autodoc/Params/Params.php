<?php

declare(strict_types=1);

namespace Autodoc\Autodoc\Params;

use ArrayObject;
use ReflectionClass;
use ReflectionNamedType;
use Autodoc\Autodoc\Exceptions\AutodocException;

/**
 * Converts method's array of params into objects with strict typed properties
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @extends ArrayObject<TKey, TValue>
 */
abstract class Params extends ArrayObject implements ParamsInterface
{
    /**
     * @param array<null|int|string, null|int|string> $data
     *
     * @throws AutodocException
     */
    public function __construct(array $data = [])
    {
        $data = array_filter(
            $data,
            static fn ($value, $key) => !in_array($value, [null, '_'], true) && is_string($key),
            ARRAY_FILTER_USE_BOTH
        );

        parent::__construct($data, ArrayObject::ARRAY_AS_PROPS);

        foreach ($data as $propName => $propValue) {
            $expectedPropType = gettype($this->$propName);
            $inputPropType = gettype($propValue);

            if ($expectedPropType !== $inputPropType) {
                throw new AutodocException("Incorrect type for property \"$propName\": expected \"$expectedPropType\", got \"$inputPropType\"");
            }

            $this->$propName = $propValue;
        }
    }
}
