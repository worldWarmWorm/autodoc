<?php

declare(strict_types=1);

namespace ApiAutodoc\Generators;

use ApiAutodoc\Enum\FIleExtension;
use ApiAutodoc\Exceptions\ApiAutodocException;
use ReflectionNamedType;

final class JsonDocumentation extends DocumentationGenerator
{
    public function process(
        string $endpoint,
        string $title,
        string $typeName,
        array $properties
    ): array
    {
        return (function() use ($endpoint, $title, $typeName, $properties): array {
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

            return $documentation;
        })();
    }

    public function save(string $fileName = 'documentation'): void
    {
        if ([] === $this->documentation) {
            throw new ApiAutodocException('Empty documentation data');
        }

        file_put_contents(
            "$fileName." . FileExtension::JSON->value,
            json_encode($this->documentation, JSON_PRETTY_PRINT)
        );
    }
}