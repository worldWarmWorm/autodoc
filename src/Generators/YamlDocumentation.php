<?php

declare(strict_types=1);

namespace ApiAutodoc\Generators;

use ReflectionClass, ReflectionNamedType, Throwable;

final class YamlDocumentation extends DocumentationGenerator
{
    public function generate(): void
    {
        $documentation = [];

        foreach ($this->endpoints as $endpoint) {
            foreach ($endpoint->getParameters() as $parameter) {
                /** @var ?ReflectionNamedType $type */
                $type = $parameter->getType();

                if (!$type?->isBuiltin()) {
                    $typeName = $type->getName();

                    if (class_exists($typeName) || interface_exists($typeName)) {
                        $reflectionClass = new ReflectionClass($typeName);

                        if ($reflectionClass->implementsInterface('ApiAutodoc\\Params\\ParamsInterface')) {
                            $endpointName = $endpoint->getName();
                            $documentation[$endpointName]['signature'] = $endpoint;
                            $documentation[$endpointName]['endpointInputType'] = $typeName;

                            foreach ($reflectionClass->getProperties() as $property) {
                                /** @var ?ReflectionNamedType $propertyType */
                                $propertyType = $property->getType();
                                $documentation[$endpointName]['props'][$property->getName()] = [
                                    'type' => $propertyType?->getName(),
                                    'isRequired' => !$propertyType?->allowsNull(),
                                    'description' => $property->getDocComment()
                                ];
                            }
                        }
                    }
                }
            }
        }

        if ([] !== $documentation) {
            $content = "# Документация эндпоинтов " . self::class . "\n\n" .  $this->arrayToYaml($documentation);

            try {
                file_put_contents('documentation.yaml', $content);
            } catch (Throwable $e) {
                throw $e;
            }
        }
    }

    /**
     * @param array<mixed, mixed> $array
     */
    private function arrayToYaml(array $array, int $indent = 0): string
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
                    $valueStr = '"' . addslashes($value instanceof \ReflectionMethod ? $value->getName() : $value) . '"';
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