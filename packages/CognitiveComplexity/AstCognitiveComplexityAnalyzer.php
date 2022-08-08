<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Symplify\PHPStanRules\CognitiveComplexity\DataCollector\CognitiveComplexityDataCollector;
use Symplify\PHPStanRules\CognitiveComplexity\NodeTraverser\ComplexityNodeTraverserFactory;
use Symplify\PHPStanRules\CognitiveComplexity\NodeVisitor\NestingNodeVisitor;

/**
 * @see \Symplify\PHPStanRules\Tests\CognitiveComplexity\AstCognitiveComplexityAnalyzer\AstCognitiveComplexityAnalyzerTest
 *
 * implements the concept described in https://www.sonarsource.com/resources/white-papers/cognitive-complexity/
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
        $totalCognitiveComplexity = 0;
        foreach ($class->getMethods() as $classMethod) {
            $totalCognitiveComplexity += $this->analyzeFunctionLike($classMethod);
        }

        return $totalCognitiveComplexity;
    }

    /**
     * @api
     * @param \PhpParser\Node\Stmt\Function_|\PhpParser\Node\Stmt\ClassMethod $functionLike
     */
    public function analyzeFunctionLike($functionLike): int
    {
        $this->cognitiveComplexityDataCollector->reset();
        $this->nestingNodeVisitor->reset();

        $nodeTraverser = $this->complexityNodeTraverserFactory->create();
        $nodeTraverser->traverse([$functionLike]);

        return $this->cognitiveComplexityDataCollector->getCognitiveComplexity();
    }
}
