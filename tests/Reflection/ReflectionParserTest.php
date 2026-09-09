<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Reflection;

use PhpParser\Node\Stmt\ClassMethod;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Symplify\PHPStanRules\NodeFinder\TypeAwareNodeFinder;
use Symplify\PHPStanRules\Reflection\ReflectionParser;
use Symplify\PHPStanRules\Tests\Reflection\Fixture\SecondClassLike;

final class ReflectionParserTest extends TestCase
{
    private ReflectionParser $reflectionParser;

    protected function setUp(): void
    {
        $this->reflectionParser = new ReflectionParser(new TypeAwareNodeFinder());
    }

    public function testParseMethodOfSecondClassLikeInFile(): void
    {
        $reflectionMethod = new ReflectionMethod(SecondClassLike::class, 'secondMethod');

        $classMethod = $this->reflectionParser->parseMethodReflection($reflectionMethod);

        $this->assertInstanceOf(ClassMethod::class, $classMethod);
        $this->assertSame('secondMethod', $classMethod->name->toString());
    }
}
