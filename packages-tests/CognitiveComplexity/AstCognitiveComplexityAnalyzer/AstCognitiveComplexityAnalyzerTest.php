<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\CognitiveComplexity\AstCognitiveComplexityAnalyzer;

use Iterator;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPStan\DependencyInjection\ContainerFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PHPStanRules\CognitiveComplexity\AstCognitiveComplexityAnalyzer;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class AstCognitiveComplexityAnalyzerTest extends TestCase
{
    private AstCognitiveComplexityAnalyzer $astCognitiveComplexityAnalyzer;

    protected function setUp(): void
    {
        $phpstanContainerFactory = new ContainerFactory(getcwd());

        $tempFile = sys_get_temp_dir() . '/_symplify_cogntive_complexity_test';
        $container = $phpstanContainerFactory->create($tempFile, [__DIR__ . '/config/configured_service.neon'], []);

        $this->astCognitiveComplexityAnalyzer = $container->getByType(AstCognitiveComplexityAnalyzer::class);
    }

    #[DataProvider('provideTokensAndExpectedCognitiveComplexity')]
    public function test(SmartFileInfo $fixtureFileInfo): never
    {
        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToInputAndExpected($fixtureFileInfo);

        $functionLike = $this->parseFileToFirstFunctionLike($inputAndExpected->getInput());
        $cognitiveComplexity = $this->astCognitiveComplexityAnalyzer->analyzeFunctionLike($functionLike);

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

    private function parseFileToFirstFunctionLike(string $fileContent): ClassMethod | Function_
    {
        $parserFactory = new ParserFactory();
        $parser = $parserFactory->create(ParserFactory::ONLY_PHP7);
        $nodes = $parser->parse($fileContent);

        $nodeFinder = new NodeFinder();
        $firstFunctionlike = $nodeFinder->findFirst(
            (array) $nodes,
            static fn (Node $node): bool => $node instanceof ClassMethod || $node instanceof Function_
        );

        if ($firstFunctionlike instanceof ClassMethod) {
            return $firstFunctionlike;
        }

        if ($firstFunctionlike instanceof Function_) {
            return $firstFunctionlike;
        }

        throw new ShouldNotHappenException();
    }
}
