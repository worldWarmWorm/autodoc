<?php

declare(strict_types=1);

namespace Autodoc;

use Autodoc\Exceptions\AutodocException;
use ReflectionMethod, ReflectionNamedType;

/**
 * @template YamlDocT of array{
 *     title: string,
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
 * @property YamlDocT $endpoints
 */
final class YamlAutodoc extends Autodoc
{
    /**
     * @inheritDoc
     */
    public function process(
        ReflectionMethod $endpoint,
        string $title,
        string $typeName,
        array $properties
    ): array
    {
        return (static function() use ($endpoint, $title, $typeName, $properties): array {
            $documentation = [];
            $endpointName = $endpoint->getName();
            $documentation['title'] = $title;
            $docComment = $endpoint->getDocComment();
            $documentation['endpoints'][$endpointName]['description'] = extractDescription($docComment);
            $documentation['endpoints'][$endpointName]['annotation'] = $docComment;
            $documentation['endpoints'][$endpointName]['inputType'] = $typeName;

            foreach ($properties as $property) {
                /** @var ?ReflectionNamedType $propertyType */
                $propertyType = $property->getType();

                if (null === $propertyType) {
                    continue;
                }

                $documentation['endpoints'][$endpointName]['params'][$property->getName()] = [
                    'type' => $propertyType->getName(),
                    'isRequired' => !$propertyType->allowsNull(),
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
            "$fileName." . Autodoc::YAML,
            arrayToYaml($this->documentation)
        );
    }

    /**
     * @return YamlDocT
     */
    public function getDocumentation(): array
    {
        return parent::getDocumentation();
    }
}