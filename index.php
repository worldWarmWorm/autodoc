<?php

interface ParamsInterface {}

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

// generator

interface DocumentationGeneratorInterface
{
    public function generate(): void;
}

abstract class DocumentationGenerator implements DocumentationGeneratorInterface
{
    /**
     * @var array<ReflectionMethod>
     */
    protected array $endpoints;

    public function __construct(ControllerInterface $controller, ?string $methodPartNameFilter = null)
    {
        $reflectionClass = new ReflectionClass($controller);
        $endpoints = $reflectionClass->getMethods();

        if (empty($endpoints)) {
            throw new Exception("Endpoints not found");
        }

        if (!is_null($methodPartNameFilter)) {
            $endpoints = array_filter(
                $reflectionClass->getMethods(),
                static fn($method) => str_contains($method->name, $methodPartNameFilter)
            );
        }

        $this->endpoints = $endpoints;
    }

}

final class JsonDocumentation extends DocumentationGenerator
{
    public function generate(): void
    {
        foreach ($this->endpoints as $endpoint) {
            try {
                foreach ($endpoint->getParameters() as $parameter) {
                    $type = $parameter->getType();

                    if ($type && !$type->isBuiltin()) {
                        $typeName = $type->getName();

                        if (class_exists($typeName) || interface_exists($typeName)) {
                            $reflectionClass = new ReflectionClass($typeName);

                            if ($reflectionClass->implementsInterface('ParamsInterface')) {
                                $endpointName = $endpoint->getName();
                                $documentation[$endpointName]['endpointInputType'] = $typeName;

                                foreach ($reflectionClass->getProperties() as $property) {
                                    $documentation[$endpointName]['props'][$property->getName()] = [
                                        'type' => $property->getType()->getName(),
                                        'isRequired' => !$property->getType()->allowsNull(),
                                        'description' => $property->getDocComment()
                                    ];
                                }
                            }
                        }
                    }
                }
            } catch (ReflectionException $e) {
                throw $e;
            }
        }

        if (isset($documentation) && [] !== $documentation) {
            $content = json_encode($documentation, JSON_PRETTY_PRINT);

            try {
                file_put_contents('documentation.json', $content);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }
}

final class YamlDocumentation extends DocumentationGenerator
{
    public function generate(): void
    {
        foreach ($this->endpoints as $endpoint) {
            try {
                foreach ($endpoint->getParameters() as $parameter) {
                    $type = $parameter->getType();

                    if ($type && !$type->isBuiltin()) {
                        $typeName = $type->getName();

                        if (class_exists($typeName) || interface_exists($typeName)) {
                            $reflectionClass = new ReflectionClass($typeName);

                            if ($reflectionClass->implementsInterface('ParamsInterface')) {
                                $endpointName = $endpoint->getName();
                                $documentation[$endpointName]['signature'] = $endpoint;
                                $documentation[$endpointName]['endpointInputType'] = $typeName;

                                foreach ($reflectionClass->getProperties() as $property) {
                                    $documentation[$endpointName]['props'][$property->getName()] = [
                                        'type' => $property->getType()->getName(),
                                        'isRequired' => !$property->getType()->allowsNull(),
                                        'description' => $property->getDocComment()
                                    ];
                                }
                            }
                        }
                    }
                }
            } catch (ReflectionException $e) {
                throw $e;
            }
        }

        if (isset($documentation) && [] !== $documentation) {
            $content = "# Документация эндпоинтов " . self::class . "\n\n" .  $this->arrayToYaml($documentation);

            try {
                file_put_contents('documentation.yaml', $content);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    private function arrayToYaml(array $array, $indent = 0): string
    {
        $yaml = '';
        $indentStr = str_repeat('  ', $indent);

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (is_int($key)) {
                    $yaml .= $this->arrayToYaml($value, $indent);
                } else {
                    $yaml .= "$indentStr$key:\n" . $this->arrayToYaml($value, $indent + 1);
                }
            } else {
                if (is_bool($value)) {
                    $valueStr = $value ? 'true' : 'false';
                } elseif (is_null($value)) {
                    $valueStr = 'null';
                } elseif (is_numeric($value)) {
                    $valueStr = $value;
                } else {
                    $valueStr = '"' . addslashes($value) . '"';
                }
                if (is_int($key)) {
                    $yaml .= "$indentStr- $valueStr\n";
                } else {
                    $yaml .= "$indentStr$key: $valueStr\n";
                }
            }
        }

        return $yaml;
    }
}


// Implementation of JsonDocumentation

interface ControllerInterface {}

class Controller implements ControllerInterface
{
    /**
     * @throws ReflectionException|Exception
     */
    public function __construct()
    {
        new JsonDocumentation($this)->generate();
        new YamlDocumentation($this)->generate();
    }

    public function getItems(ItemsParams $params): array
    {
        return [
            'success' => true,
            'message' => 'Items retrieved',
        ];
    }
}

