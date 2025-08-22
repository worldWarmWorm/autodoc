<?php

declare(strict_types=1);

namespace ApiAutodoc\Generators;

use ReflectionClass, ReflectionNamedType, Throwable;

final class JsonDocumentation extends DocumentationGenerator
{
    private const string JSON = 'json';

    public function generate(string $title, string $file = 'documentation'): void
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
                            $documentation['_comment'] = $title;
                            $documentation['endpoints'][$endpointName]['endpointInputType'] = $typeName;

                            foreach ($reflectionClass->getProperties() as $property) {
                                /** @var ?ReflectionNamedType $propertyType */
                                $propertyType = $property->getType();
                                $documentation['endpoints'][$endpointName]['props'][$property->getName()] = [
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
            $content = json_encode($documentation, JSON_PRETTY_PRINT);

            file_put_contents("$file." . self::JSON, $content);
        }
    }
}