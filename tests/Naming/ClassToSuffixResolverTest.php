<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Naming;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Naming\ClassToSuffixResolver;

final class ClassToSuffixResolverTest extends TestCase
{
    private ClassToSuffixResolver $classToSuffixResolver;

    protected function setUp(): void
    {
        $this->classToSuffixResolver = new ClassToSuffixResolver();
    }

    #[DataProvider('provideData')]
    public function test(string $className, string $expectedSuffix): void
    {
        $resolvedSuffix = $this->classToSuffixResolver->resolveFromClass($className);
        $this->assertSame($expectedSuffix, $resolvedSuffix);
    }

    /**
     * @return Iterator<string[]>
     */
    public static function provideData(): Iterator
    {
        yield ['Exception', 'Exception'];
        yield ['Symfony\Component\Console\Command\Command', 'Command'];
        yield [TestCase::class, 'Test'];
        yield ['Symfony\Component\EventDispatcher\EventSubscriberInterface', 'EventSubscriber'];
    }
}
