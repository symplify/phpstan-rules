<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node;
use PhpParser\NodeFinder;

/**
 * @todo remove after https://github.com/nikic/PHP-Parser/pull/869 is released
 */
final class TypeAwareNodeFinder
{
    /**
     * @readonly
     */
    private NodeFinder $nodeFinder;

    public function __construct()
    {
        $this->nodeFinder = new NodeFinder();
    }

    /**
     * @template TNode as Node
     *
     * @param mixed[]|\PhpParser\Node $nodes
     * @param class-string<TNode> $type
     * @return TNode|null
     */
    public function findFirstInstanceOf($nodes, string $type): ?Node
    {
        return $this->nodeFinder->findFirstInstanceOf($nodes, $type);
    }
}
