<?php

declare(strict_types=1);

namespace Autodoc;

use Autodoc\Exceptions\AutodocException;
use ReflectionMethod, ReflectionNamedType;

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
            $documentation['endpoints'][$endpointName]['annotation'] = $endpoint->getDocComment();
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

    public function save(string $fileName = 'autodoc'): void
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