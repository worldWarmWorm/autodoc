<?php

declare(strict_types=1);

namespace Autodoc\Autodoc;

use Autodoc\Autodoc\Exceptions\AutodocException;
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
        return (function() use ($endpoint, $title, $typeName, $properties): array {
            $documentation = [];
            $endpointName = $endpoint->getName();
            $documentation['title'] = $title;
            $docComment = $endpoint->getDocComment();
            $documentation['endpoints'][$endpointName]['description'] = $this->extractDescription($docComment);
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
            $this->arrayToYaml($this->documentation)
        );
    }

    /**
     * @return YamlDocT
     */
    public function getDocumentation(): array
    {
        return parent::getDocumentation();
    }

    /**
     * @param array<string|int, mixed> $array
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
                    $valueStr = '"' . addslashes($value instanceof ReflectionMethod ? $value->getName() : $value) . '"';
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