<?php

declare(strict_types=1);

namespace ApiAutodoc\Params;

use ArrayObject, ReflectionClass;
use ReflectionNamedType;

abstract class Params extends ArrayObject implements ParamsInterface
{
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
                throw new \Exception("Property \"$name\" is required");
            }

            $value = $this[$name];
            /** @var ?ReflectionNamedType $type */
            $type = $property->getType();

            if (null === $type) {
                continue;
            }

            if (!$this->checkType($value, $type)) {
                throw new \Exception("Incorrect type for property \"$name\": expected {$type->getName()}, got " . gettype($value));
            }
        }
    }

    private function checkType($value, ReflectionNamedType $type): bool
    {
        if ($type->isBuiltin()) {
            return match ($type->getName()) {
                'int' => is_int($value),
                'string' => is_string($value),
                'bool' => is_bool($value),
                'array' => is_array($value),
                'float' => is_float($value),
                default => true,
            };
        } else {
            $class = $type->getName();
            return $value instanceof $class;
        }
    }
}