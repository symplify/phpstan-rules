<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class ClassNamespaceAndDirectory
{
    /**
     * @readonly
     * @var string
     */
    private $namespace;
    /**
     * @readonly
     * @var string
     */
    private $directory;
    /**
     * @readonly
     * @var string
     */
    private $namespaceBeforeClass;
    public function __construct(string $namespace, string $directory, string $namespaceBeforeClass)
    {
        $this->namespace = $namespace;
        $this->directory = $directory;
        $this->namespaceBeforeClass = $namespaceBeforeClass;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getSingleDirectory(): string
    {
        return $this->directory;
    }

    public function getNamespaceBeforeClass(): string
    {
        return $this->namespaceBeforeClass;
    }
}
