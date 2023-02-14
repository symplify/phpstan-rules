<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\CognitiveComplexity\CompositionOverInheritanceAnalyzer;

use Iterator;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPStan\DependencyInjection\ContainerFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PHPStanRules\CognitiveComplexity\CompositionOverInheritanceAnalyzer;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class CompositionOverInheritanceAnalyzerTest extends TestCase
{
    private CompositionOverInheritanceAnalyzer $compositionOverInheritanceAnalyzer;

    protected function setUp(): void
    {
        $phpstanContainerFactory = new ContainerFactory(getcwd());

        $tempFile = sys_get_temp_dir() . '/_symplify_cogntive_complexity_composition_test';
        $container = $phpstanContainerFactory->create($tempFile, [__DIR__ . '/config/configured_service.neon'], []);

        $this->compositionOverInheritanceAnalyzer = $container->getByType(CompositionOverInheritanceAnalyzer::class);
    }

    #[DataProvider('provideTokensAndExpectedCognitiveComplexity')]
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToInputAndExpected($fixtureFileInfo);

        $classLike = $this->parseFileToFirstClass($inputAndExpected->getInput());
        $cognitiveComplexity = $this->compositionOverInheritanceAnalyzer->analyzeClassLike($classLike);

        $this->assertSame((int) $inputAndExpected->getExpected(), $cognitiveComplexity);
    }

    /**
     * Here are tested all examples from https://www.sonarsource.com/docs/CognitiveComplexity.pdf
     *
     * @return Iterator<mixed, SmartFileInfo[]>
     */
    public static function provideTokensAndExpectedCognitiveComplexity(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Source');
    }

    private function parseFileToFirstClass(string $fileContent): Class_
    {
        $parserFactory = new ParserFactory();
        $parser = $parserFactory->create(ParserFactory::ONLY_PHP7);
        $nodes = $parser->parse($fileContent);

        $nodeFinder = new NodeFinder();
        $firstClass = $nodeFinder->findFirst((array) $nodes, static fn (Node $node): bool => $node instanceof Class_);

        if (! $firstClass instanceof Class_) {
            throw new ShouldNotHappenException();
        }

        return $firstClass;
    }
}
