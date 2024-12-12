<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Reflection;

use Nette\Utils\FileSystem;
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
use Symplify\PHPStanRules\NodeFinder\TypeAwareNodeFinder;
use Throwable;

final class ReflectionParser
{
    /**
     * @readonly
     */
    private TypeAwareNodeFinder $typeAwareNodeFinder;
    /**
     * @var array<string, ClassLike>
     */
    private array $classesByFilename = [];

    /**
     * @readonly
     */
    private Parser $parser;

    public function __construct(
        TypeAwareNodeFinder $typeAwareNodeFinder
    ) {
        $this->typeAwareNodeFinder = $typeAwareNodeFinder;
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->createForNewestSupportedVersion();
    }

    /**
     * @param \ReflectionMethod|\PHPStan\Reflection\MethodReflection $reflectionMethod
     */
    public function parseMethodReflection($reflectionMethod): ?ClassMethod
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

        return $this->parseFilenameToClass($fileName);
    }

    /**
     * @param \ReflectionClass|\PHPStan\Reflection\ClassReflection $reflectionClass
     */
    private function parseNativeClassReflection($reflectionClass): ?ClassLike
    {
        $fileName = $reflectionClass->getFileName();
        if ($fileName === false) {
            return null;
        }

        if ($fileName === null) {
            return null;
        }

        return $this->parseFilenameToClass($fileName);
    }

    private function parseFilenameToClass(string $fileName): ?\PhpParser\Node\Stmt\ClassLike
    {
        if (isset($this->classesByFilename[$fileName])) {
            return $this->classesByFilename[$fileName];
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
        } catch (Throwable $exception) {
            // not reachable
            return null;
        }

        $classLike = $this->typeAwareNodeFinder->findFirstInstanceOf($stmts, ClassLike::class);
        if (! $classLike instanceof ClassLike) {
            return null;
        }

        $this->classesByFilename[$fileName] = $classLike;

        return $classLike;
    }
}
