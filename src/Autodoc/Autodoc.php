<?php

declare(strict_types=1);

namespace Autodoc\Autodoc;

use Autodoc\Autodoc\Exceptions\AutodocException;
use ReflectionClass, ReflectionNamedType, ReflectionMethod;

abstract class Autodoc implements AutodocInterface
{
    public const JSON = 'json';
    public const YAML = 'yaml';

    /**
     * @var array<ReflectionMethod>
     */
    protected array $endpoints;

    /**
     * @var array<mixed>
     */
    protected array $documentation = [];

    /**
     * @throws AutodocException
     */
    public function __construct(EndpointInterface $controller, ?string $methodPartNameFilter = null)
    {
        $reflectionClass = new \ReflectionClass($controller);
        $endpoints = array_filter(
            $reflectionClass->getMethods(),
            static fn(ReflectionMethod $method) => strpos($method->name, '__') === false
        );

        if ($endpoints === []) {
            throw new AutodocException("Endpoints not found");
        }

        if (null !== $methodPartNameFilter) {
            $endpoints = array_filter(
                $reflectionClass->getMethods(),
                static fn($method) => strpos($method->name, $methodPartNameFilter) !== false
            );
        }

        $this->endpoints = $endpoints;
    }

    public function generate(string $title, string $fileName): void
    {
        foreach ($this->endpoints as $endpoint) {
            foreach ($endpoint->getParameters() as $parameter) {
                /** @var ?ReflectionNamedType $type */
                $type = $parameter->getType();

                if ($type === null || $type->isBuiltin() === true) {
                    continue;
                }

                $typeName = $type->getName();

                if (class_exists($typeName) === false && interface_exists($typeName) === false) {
                    continue;
                }

                $reflectionClass = new ReflectionClass($typeName);

                if (false === $reflectionClass->implementsInterface('Autodoc\\Autodoc\\Params\\ParamsInterface')) {
                    continue;
                }

                $this->documentation[] = $this->process(
                    $reflectionClass,
                    $endpoint,
                    $title,
                    $typeName
                );
            }
        }

        $this->save($fileName);
    }

    /**
     * Method delivers all generated data for documentation in case you need to output it somewhere
     *
     * @return array<mixed>
     */
    public function getDocumentation(): array
    {
        return $this->documentation;
    }

    protected function extractDescription(string $string): string
    {
        return trim(preg_replace('/.*@autodocDescription\s+([^*]+)\*.*/s', '$1', $string) ?? '');
    }
}