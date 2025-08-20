<?php

declare(strict_types=1);

namespace generators;

use ReflectionClass, ReflectionException, Throwable;

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
            } catch (Throwable $e) {
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