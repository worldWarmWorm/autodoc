<?php

declare(strict_types=1);

namespace ApiAutodoc\Generators;

use ReflectionClass, ReflectionNamedType, Throwable;

final class JsonDocumentation extends DocumentationGenerator
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
            $content = json_encode($documentation, JSON_PRETTY_PRINT);

            try {
                file_put_contents('documentation.json', $content);
            } catch (Throwable $e) {
                throw $e;
            }
        }
    }
}