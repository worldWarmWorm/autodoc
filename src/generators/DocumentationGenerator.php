<?php

declare(strict_types=1);

namespace generators;

use params\EndpointInterface, ReflectionClass, ReflectionMethod;

abstract class DocumentationGenerator implements DocumentationGeneratorInterface
{
    /**
     * @var array<ReflectionMethod>
     */
    protected array $endpoints;

    public function __construct(EndpointInterface $controller, ?string $methodPartNameFilter = null)
    {
        $reflectionClass = new ReflectionClass($controller);
        $endpoints = $reflectionClass->getMethods();

        if (empty($endpoints)) {
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