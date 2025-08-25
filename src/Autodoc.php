<?php

declare(strict_types=1);

namespace Autodoc;

use Autodoc\Exceptions\AutodocException;
use ReflectionClass, ReflectionMethod, ReflectionNamedType;

abstract class Autodoc implements AutodocInterface
{
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
            static fn(ReflectionMethod $method) => !str_contains($method->name, '__')
        );

        if ($endpoints === []) {
            throw new AutodocException("Endpoints not found");
        }

        if (!is_null($methodPartNameFilter)) {
            $endpoints = array_filter(
                $reflectionClass->getMethods(),
                static fn($method) => str_contains($method->name, $methodPartNameFilter)
            );
        }

        $this->endpoints = $endpoints;
    }

    public function generate(string $title, string $fileName = 'autodoc'): void
    {
        foreach ($this->endpoints as $endpoint) {
            foreach ($endpoint->getParameters() as $parameter) {
                /** @var ?ReflectionNamedType $type */
                $type = $parameter->getType();

                if (!$type?->isBuiltin()) {
                    $typeName = $type->getName();

                    if (class_exists($typeName) || interface_exists($typeName)) {
                        $reflectionClass = new ReflectionClass($typeName);

                        if ($reflectionClass->implementsInterface('Autodoc\\Params\\ParamsInterface')) {
                            $this->documentation[] = $this->process(
                                $endpoint,
                                $title,
                                $typeName,
                                $reflectionClass->getProperties()
                            );
                        }
                    }
                }
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
}