<?php

declare(strict_types=1);

namespace ApiAutodoc\Generators;

use ReflectionClass, ReflectionException, Throwable;

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

                            if ($reflectionClass->implementsInterface('EndpointInterface')) {
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
            } catch (Throwable $e) {
                throw $e;
            }
        }
    }
}