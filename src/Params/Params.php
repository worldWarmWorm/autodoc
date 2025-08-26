<?php

declare(strict_types=1);

namespace Autodoc\Params;

use ArrayObject, ReflectionClass, ReflectionNamedType;
use Autodoc\Exceptions\AutodocException;

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
            static fn ($key, $value) => !in_array($value, [null, '', '_'], true),
            ARRAY_FILTER_USE_BOTH
        );

        parent::__construct($data, ArrayObject::ARRAY_AS_PROPS);
        self::validateProperties();
    }

    /**
     * @throws AutodocException
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
                throw new AutodocException("Property \"$name\" is required");
            }

            $value = $this[$name];
            /** @var ?ReflectionNamedType $type */
            $type = $property->getType();

            if (null === $type) {
                continue;
            }

            if (false === $this->checkType($value, $type)) {
                throw new AutodocException("Incorrect type for property \"$name\": expected {$type->getName()}, got " . gettype($value));
            }
        }
    }

    /**
     * @param mixed $value
     */
    private function checkType($value, ReflectionNamedType $type): bool
    {
        $typeName = $type->getName();

        if ($type->isBuiltin()) {
            switch ($typeName) {
                case 'int':
                    $isTypeOf = is_int($value) || $value === null;
                    break;
                case 'string':
                    $isTypeOf = is_string($value);
                    break;
                case 'bool':
                    $isTypeOf = is_bool($value);
                    break;
                case 'array':
                    $isTypeOf = is_array($value);
                    break;
                case 'float':
                    $isTypeOf = is_float($value);
                    break;
                default:
                    $isTypeOf = true;
            }
        } else {
            $isTypeOf = $value instanceof $typeName;
        }

        return $isTypeOf;
    }
}