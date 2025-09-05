<?php

declare(strict_types=1);

namespace Autodoc\Autodoc;

use Autodoc\Autodoc\Enum\FileExtensionEnum;
use Autodoc\Autodoc\Exceptions\AutodocException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

/**
 * @template YamlDocT of array{
 *     title: string,
 *     endpoints: array<string, array{
 *         description: string,
 *         annotation: string,
 *         inputType: string,
 *         params: array<string, array{
 *             type: string,
 *             defaultValue: bool | int | string | array,
 *             annotation: string
 *         }>,
 *         returnType: string
 *      }>
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
        ReflectionClass $reflectionClass,
        ReflectionMethod $endpoint,
        string $title,
        string $typeName
    ): array {
        return (function () use ($reflectionClass, $endpoint, $title, $typeName): array {
            $documentation = [];
            $endpointName = $endpoint->getName();
            $documentation['title'] = $title;
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

            $documentation['endpoints'][$endpointName]['returnType'] = $endpoint->getReturnType()?->getName();

            return $documentation;
        })();
    }

    public function save(string $fileName): void
    {
        if ([] === $this->documentation) {
            throw new AutodocException('Empty documentation data');
        }

        file_put_contents(
            "$fileName." . FileExtensionEnum::YAML->value,
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
