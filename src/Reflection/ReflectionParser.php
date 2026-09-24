<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Reflection;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use ReflectionClass;
use ReflectionMethod;
use Symplify\PHPStanRules\FileSystem\FileSystem;
use Symplify\PHPStanRules\NodeFinder\TypeAwareNodeFinder;
use Throwable;

final class ReflectionParser
{
    /**
     * @var array<string, ClassLike>
     */
    private array $classesByName = [];

    private readonly Parser $parser;

    public function __construct(
        private readonly TypeAwareNodeFinder $typeAwareNodeFinder
    ) {
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->createForNewestSupportedVersion();
    }

    public function parseMethodReflection(ReflectionMethod|MethodReflection $reflectionMethod): ?ClassMethod
    {
        $classLike = $this->parseNativeClassReflection($reflectionMethod->getDeclaringClass());
        if (! $classLike instanceof ClassLike) {
            return null;
        }

        return $classLike->getMethod($reflectionMethod->getName());
    }

    /**
     * @api used by extensions
     */
    public function parseClassReflection(ClassReflection $classReflection): ?ClassLike
    {
        $fileName = $classReflection->getFileName();
        if ($fileName === null) {
            return null;
        }

        return $this->parseFilenameToClass($fileName, $classReflection->getName());
    }

    private function parseNativeClassReflection(ReflectionClass|ClassReflection $reflectionClass): ?ClassLike
    {
        $fileName = $reflectionClass->getFileName();
        if ($fileName === false) {
            return null;
        }

        if ($fileName === null) {
            return null;
        }

        return $this->parseFilenameToClass($fileName, $reflectionClass->getName());
    }

    private function parseFilenameToClass(string $fileName, string $className): ClassLike|null
    {
        if (isset($this->classesByName[$className])) {
            return $this->classesByName[$className];
        }

        try {
            $stmts = $this->parser->parse(FileSystem::read($fileName));
            if (! is_array($stmts)) {
                return null;
            }

            // complete namespacedName variables
            $nodeTraverser = new NodeTraverser();
            $nodeTraverser->addVisitor(new NameResolver());
            $nodeTraverser->traverse($stmts);
        } catch (Throwable) {
            // not reachable
            return null;
        }

        // a file can hold multiple class-likes, match the requested one by name
        $classLike = $this->typeAwareNodeFinder->findFirst(
            $stmts,
            static fn (Node $node): bool => $node instanceof ClassLike && (string) $node->namespacedName === $className
        );

        if (! $classLike instanceof ClassLike) {
            return null;
        }

        $this->classesByName[$className] = $classLike;

        return $classLike;
    }
}
