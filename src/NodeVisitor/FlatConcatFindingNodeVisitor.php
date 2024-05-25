<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Symplify\PHPStanRules\NodeAnalyzer\FileCheckingFuncCallAnalyzer;

final class FlatConcatFindingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @readonly
     * @var \Symplify\PHPStanRules\NodeAnalyzer\FileCheckingFuncCallAnalyzer
     */
    private $fileCheckingFuncCallAnalyzer;
    /**
     * @var Concat[]
     */
    private $foundNodes = [];

    public function __construct(FileCheckingFuncCallAnalyzer $fileCheckingFuncCallAnalyzer)
    {
        $this->fileCheckingFuncCallAnalyzer = $fileCheckingFuncCallAnalyzer;
    }

    /**
     * @param Node[] $nodes
     */
    public function beforeTraverse(array $nodes): ?array
    {
        $this->foundNodes = [];
        return null;
    }

    /**
     * @return int|\PhpParser\Node|null
     */
    public function enterNode(Node $node)
    {
        if ($this->fileCheckingFuncCallAnalyzer->isFileExistCheck($node)) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        if (! $node instanceof Concat) {
            return null;
        }

        if ($node->left instanceof Concat) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        if ($node->right instanceof Concat) {
            return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
        }

        $this->foundNodes[] = $node;
        return null;
    }

    /**
     * @return Concat[]
     */
    public function getFoundNodes(): array
    {
        return $this->foundNodes;
    }
}
