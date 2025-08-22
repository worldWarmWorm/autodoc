<?php

declare(strict_types=1);

namespace ApiAutodoc\Generators;

use ApiAutodoc\Exceptions\ApiAutodocException;
use ReflectionClass, ReflectionNamedType, Throwable;

final class JsonDocumentation extends DocumentationGenerator
{
    private const string JSON = 'json';
    
    public function process(string $endpoint, string $title, string $typeName, array $properties): callable
    {
        return function (string $endpoint, string $title, string $typeName, array $properties): void {
            $documentation['_comment'] = $title;
            $documentation['endpoints'][$endpoint]['endpointInputType'] = $typeName;

            foreach ($properties as $property) {
                /** @var ?ReflectionNamedType $propertyType */
                $propertyType = $property->getType();
                $documentation['endpoints'][$endpoint]['props'][$property->getName()] = [
                    'type' => $propertyType?->getName(),
                    'isRequired' => !$propertyType?->allowsNull(),
                    'description' => $property->getDocComment()
                ];
            }
        };
    }

    public function save(string $fileName = 'documentation'): void
    {
        if ([] === $this->documentation) {
            throw new ApiAutodocException('Empty documentation data');
        }

        file_put_contents(
            "$fileName." . self::JSON,
            json_encode($this->documentation, JSON_PRETTY_PRINT)
        );
    }
}