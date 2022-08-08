<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity\NodeTraverser;

use PhpParser\NodeTraverser;
use Symplify\PHPStanRules\CognitiveComplexity\NodeVisitor\ComplexityNodeVisitor;
use Symplify\PHPStanRules\CognitiveComplexity\NodeVisitor\NestingNodeVisitor;

final class ComplexityNodeTraverserFactory
{
    /**
     * @var \Symplify\PHPStanRules\CognitiveComplexity\NodeVisitor\NestingNodeVisitor
     */
    private $nestingNodeVisitor;
    /**
     * @var \Symplify\PHPStanRules\CognitiveComplexity\NodeVisitor\ComplexityNodeVisitor
     */
    private $complexityNodeVisitor;
    public function __construct(NestingNodeVisitor $nestingNodeVisitor, ComplexityNodeVisitor $complexityNodeVisitor)
    {
        $this->nestingNodeVisitor = $nestingNodeVisitor;
        $this->complexityNodeVisitor = $complexityNodeVisitor;
    }

    public function create(): NodeTraverser
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($this->nestingNodeVisitor);
        $nodeTraverser->addVisitor($this->complexityNodeVisitor);

        return $nodeTraverser;
    }
}
