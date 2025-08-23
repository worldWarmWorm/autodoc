<?php

declare(strict_types=1);

namespace ApiAutodoc\Params;

use ApiAutodoc\Exceptions\ApiAutodocException;
use ArrayObject, ReflectionClass, ReflectionNamedType;

/**
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
     * @throws ApiAutodocException
     */
    public function __construct(array $data = [])
    {
        // @TODO need test to check filter useless keys
        $data = array_filter(
            $data,
            static fn ($key, $value) => !in_array($value, [null, '', '_'], true),
            ARRAY_FILTER_USE_BOTH
        );

        parent::__construct($data, ArrayObject::ARRAY_AS_PROPS);
        self::validateProperties();
    }

    /**
     * @throws ApiAutodocException
     */
    public function validateProperties(): void
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            if ($property->getDeclaringClass()->getName() !== get_class($this)) {
                continue;
            }

            $name = $property->getName();

            if (!$this->offsetExists($name)) {
                throw new ApiAutodocException("Property \"$name\" is required");
            }

            $value = $this[$name];
            /** @var ?ReflectionNamedType $type */
            $type = $property->getType();

            if (null === $type) {
                continue;
            }

            if (false === $this->checkType($value, $type)) {
                throw new ApiAutodocException("Incorrect type for property \"$name\": expected {$type->getName()}, got " . gettype($value));
            }
        }
    }

    private function checkType(mixed $value, ReflectionNamedType $type): bool
    {
        $typeName = $type->getName();

        if ($type->isBuiltin()) {
            $isTypeOf = match ($typeName) {
                'int' => is_int($value) || $value === null,
                'string' => is_string($value),
                'bool' => is_bool($value),
                'array' => is_array($value),
                'float' => is_float($value),
                default => true,
            };
        } else {
            $isTypeOf = $value instanceof $typeName;
        }

        return $isTypeOf;
    }
}