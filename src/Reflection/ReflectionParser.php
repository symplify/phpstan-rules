<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Reflection;

use Nette\Utils\FileSystem;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Symplify\PHPStanRules\NodeFinder\TypeAwareNodeFinder;
use Throwable;

/**
 * @api
 */
final class ReflectionParser
{
    /**
     * @readonly
     * @var \Symplify\PHPStanRules\NodeFinder\TypeAwareNodeFinder
     */
    private $typeAwareNodeFinder;
    /**
     * @var array<string, ClassLike>
     */
    private $classesByFilename = [];

    /**
     * @readonly
     * @var \PhpParser\Parser
     */
    private $parser;

    public function __construct(
        TypeAwareNodeFinder $typeAwareNodeFinder
    ) {
        $this->typeAwareNodeFinder = $typeAwareNodeFinder;
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
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

    public function parsePropertyReflection(ReflectionProperty $reflectionProperty): ?Property
    {
        $class = $this->parseNativeClassReflection($reflectionProperty->getDeclaringClass());
        if (! $class instanceof ClassLike) {
            return null;
        }

        return $class->getProperty($reflectionProperty->getName());
    }

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
