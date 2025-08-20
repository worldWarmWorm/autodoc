<?php

declare(strict_types=1);

namespace ApiAutodoc\Params;

use ArrayObject, ReflectionClass;

abstract class Params extends ArrayObject implements ParamsInterface
{
    public function __construct(array $data = [])
    {
        if (in_array('_', $data)) {
            unset($data['_']);
        }

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
            $type = $property->getType();

            if (is_null($type)) {
                continue;
            }

            if (!$this->checkType($value, $type)) {
                throw new \Exception("Incorrect type for property \"$name\": expected {$type->getName()}, got " . gettype($value));
            }
        }
    }

    private function checkType($value, ReflectionType $type): bool
    {
        if ($type instanceof \ReflectionNamedType) {
            if ($type->isBuiltin()) {
                switch ($type->getName()) {
                    case 'int':
                        return is_int($value);
                    case 'string':
                        return is_string($value);
                    case 'bool':
                        return is_bool($value);
                    case 'array':
                        return is_array($value);
                    case 'float':
                        return is_float($value);
                    default:
                        return true;
                }
            } else {
                $class = $type->getName();
                return $value instanceof $class;
            }
        }

        return false;
    }
}