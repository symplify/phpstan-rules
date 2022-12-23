<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Symplify\PHPStanRules\CognitiveComplexity\DataCollector\CognitiveComplexityDataCollector;
use Symplify\PHPStanRules\CognitiveComplexity\NodeTraverser\ComplexityNodeTraverserFactory;
use Symplify\PHPStanRules\CognitiveComplexity\NodeVisitor\NestingNodeVisitor;
use Symplify\PHPStanRules\Exception\DeprecatedException;

/**
 * @see \Symplify\PHPStanRules\Tests\CognitiveComplexity\AstCognitiveComplexityAnalyzer\AstCognitiveComplexityAnalyzerTest
 *
 * implements the concept described in https://www.sonarsource.com/resources/white-papers/cognitive-complexity/
 *
 * @deprecated
 */
final class AstCognitiveComplexityAnalyzer
{
    /**
     * @var \Symplify\PHPStanRules\CognitiveComplexity\NodeTraverser\ComplexityNodeTraverserFactory
     */
    private $complexityNodeTraverserFactory;
    /**
     * @var \Symplify\PHPStanRules\CognitiveComplexity\DataCollector\CognitiveComplexityDataCollector
     */
    private $cognitiveComplexityDataCollector;
    /**
     * @var \Symplify\PHPStanRules\CognitiveComplexity\NodeVisitor\NestingNodeVisitor
     */
    private $nestingNodeVisitor;
    public function __construct(ComplexityNodeTraverserFactory $complexityNodeTraverserFactory, CognitiveComplexityDataCollector $cognitiveComplexityDataCollector, NestingNodeVisitor $nestingNodeVisitor)
    {
        $this->complexityNodeTraverserFactory = $complexityNodeTraverserFactory;
        $this->cognitiveComplexityDataCollector = $cognitiveComplexityDataCollector;
        $this->nestingNodeVisitor = $nestingNodeVisitor;
    }

    public function analyzeClassLike(Class_ $class): int
    {
        $deprecatedMessage = sprintf(
            'The "%s" service was deprecated and moved to "%s" package that has much simpler configuration. Use it instead.',
            self::class,
            'https://github.com/TomasVotruba/cognitive-complexity'
        );
        throw new DeprecatedException($deprecatedMessage);
    }

    /**
     * @api
     * @param \PhpParser\Node\Stmt\Function_|\PhpParser\Node\Stmt\ClassMethod $functionLike
     */
    public function analyzeFunctionLike($functionLike): int
    {
        $deprecatedMessage = sprintf(
            'The "%s" service was deprecated and moved to "%s" package that has much simpler configuration. Use it instead.',
            self::class,
            'https://github.com/TomasVotruba/cognitive-complexity'
        );
        throw new DeprecatedException($deprecatedMessage);
    }
}
