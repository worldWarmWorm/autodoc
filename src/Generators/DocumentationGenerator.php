<?php

declare(strict_types=1);

namespace ApiAutodoc\Generators;

use ApiAutodoc\Endpoints\EndpointInterface;
use ReflectionMethod;

abstract class DocumentationGenerator implements DocumentationGeneratorInterface
{
    /**
     * @var array<ReflectionMethod>
     */
    protected array $endpoints;

    /**
     * @throws ApiAutodocException
     */
    public function __construct(EndpointInterface $controller, ?string $methodPartNameFilter = null)
    {
        $reflectionClass = new \ReflectionClass($controller);
        $endpoints = array_filter(
            $reflectionClass->getMethods(),
            static fn(ReflectionMethod $method) => !str_contains($method->name, '__')
        );

        if ($endpoints === []) {
            throw new ApiAutodocException("Endpoints not found");
        }

        if (!is_null($methodPartNameFilter)) {
            $endpoints = array_filter(
                $reflectionClass->getMethods(),
                static fn($method) => str_contains($method->name, $methodPartNameFilter)
            );
        }

        $this->endpoints = $endpoints;
    }
}