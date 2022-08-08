<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Symplify\PHPStanRules\CognitiveComplexity\DataCollector\CognitiveComplexityDataCollector;
use Symplify\PHPStanRules\CognitiveComplexity\NodeAnalyzer\ComplexityAffectingNodeFinder;

final class ComplexityNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var \Symplify\PHPStanRules\CognitiveComplexity\DataCollector\CognitiveComplexityDataCollector
     */
    private $cognitiveComplexityDataCollector;
    /**
     * @var \Symplify\PHPStanRules\CognitiveComplexity\NodeAnalyzer\ComplexityAffectingNodeFinder
     */
    private $complexityAffectingNodeFinder;
    public function __construct(CognitiveComplexityDataCollector $cognitiveComplexityDataCollector, ComplexityAffectingNodeFinder $complexityAffectingNodeFinder)
    {
        $this->cognitiveComplexityDataCollector = $cognitiveComplexityDataCollector;
        $this->complexityAffectingNodeFinder = $complexityAffectingNodeFinder;
    }

    public function enterNode(Node $node): ?Node
    {
        if (! $this->complexityAffectingNodeFinder->isIncrementingNode($node)) {
            return null;
        }

        $this->cognitiveComplexityDataCollector->increaseOperation();

        return null;
    }
}
