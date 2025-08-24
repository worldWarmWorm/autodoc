<?php

declare(strict_types=1);

namespace Autodoc\Autodoc;

use Autodoc\Autodoc\Enum\FileExtension;
use Autodoc\Autodoc\Exceptions\AutodocException;
use ReflectionMethod;
use ReflectionNamedType;

final class JsonAutodoc extends Autodoc
{
    public function process(
        ReflectionMethod $endpoint,
        string $title,
        string $typeName,
        array $properties
    ): array
    {
        return (static function() use ($endpoint, $title, $typeName, $properties): array {
            $endpointName = $endpoint->getName();
            $documentation['_comment'] = $title;
            $documentation['endpoints'][$endpointName]['annotation'] = $endpoint->getDocComment();
            $documentation['endpoints'][$endpointName]['endpointInputType'] = $typeName;

            foreach ($properties as $property) {
                /** @var ?ReflectionNamedType $propertyType */
                $propertyType = $property->getType();
                $documentation['endpoints'][$endpointName]['params'][$property->getName()] = [
                    'type' => $propertyType?->getName(),
                    'isRequired' => !$propertyType?->allowsNull(),
                    'annotation' => $property->getDocComment()
                ];
            }

            $documentation['endpoints'][$endpointName]['returnType'] = $endpoint->getReturnType()->getName();

            return $documentation;
        })();
    }

    public function save(string $fileName = 'autodoc'): void
    {
        if ([] === $this->documentation) {
            throw new AutodocException('Empty documentation data');
        }

        file_put_contents(
            "$fileName." . FileExtension::JSON->value,
            json_encode($this->documentation, JSON_PRETTY_PRINT)
        );
    }
}