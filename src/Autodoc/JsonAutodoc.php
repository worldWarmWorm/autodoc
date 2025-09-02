<?php

declare(strict_types=1);

namespace Autodoc\Autodoc;

use Autodoc\Api\Product\Controller\ProductParams;
use Autodoc\Autodoc\Exceptions\AutodocException;
use ReflectionClass, ReflectionMethod, ReflectionNamedType;

/**
 * @template JsonDocT of array{
 *     _comment: string,
 *     endpoints: array<string, array{
 *         annotation: string,
 *         inputType: string,
 *         params: array<string, array{
 *             type: string,
 *             isRequired: bool,
 *             annotation: string
 *         }>,
 *         returnType: string
 *     }>
 * }
 *
 * @property JsonDocT $endpoints
 */
final class JsonAutodoc extends Autodoc
{
    /**
     * @inheritDoc
     */
    public function process(
        ReflectionClass $reflectionClass,
        ReflectionMethod $endpoint,
        string $title,
        string $typeName
    ): array
    {
        return (function() use ($reflectionClass, $endpoint, $title, $typeName): array {
            $documentation = [];
            $endpointName = $endpoint->getName();
            $documentation['_comment'] = $title;
            $docComment = $endpoint->getDocComment();
            $documentation['endpoints'][$endpointName]['description'] = $this->extractDescription($docComment);
            $documentation['endpoints'][$endpointName]['annotation'] = $docComment;
            $documentation['endpoints'][$endpointName]['inputType'] = $typeName;

            foreach ($reflectionClass->getProperties() as $property) {
                /** @var ?ReflectionNamedType $propertyType */
                $propertyType = $property->getType();

                if (null === $propertyType) {
                    continue;
                }

                $documentation['endpoints'][$endpointName]['params'][$property->getName()] = [
                    'type' => $propertyType->getName(),
                    'defaultValue' => $property->isDefault() ? $property->getValue($reflectionClass->newInstance()) : false,
                    'annotation' => $property->getDocComment()
                ];
            }

            /** @var ?ReflectionNamedType $returnType */
            $returnType = $endpoint->getReturnType();

            if (null !== $returnType) {
                $documentation['endpoints'][$endpointName]['returnType'] = $returnType->getName();
            }

            return $documentation;
        })();
    }

    public function save(string $fileName): void
    {
        if ([] === $this->documentation) {
            throw new AutodocException('Empty documentation data');
        }

        file_put_contents(
            "$fileName." . Autodoc::JSON,
            json_encode($this->documentation, JSON_PRETTY_PRINT)
        );
    }

    /**
     * @return JsonDocT
     */
    public function getDocumentation(): array
    {
        return parent::getDocumentation();
    }
}