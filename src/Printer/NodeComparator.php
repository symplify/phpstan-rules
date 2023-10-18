<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Printer;

use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard;
use Symplify\PHPStanRules\Enum\AttributeKey;

final class NodeComparator
{
    public function __construct(
        private readonly Standard $standard
    ) {
    }

    public function areNodesEqual(Node $firstNode, Node $secondNode): bool
    {
        // remove comments from nodes
        $firstNode->setAttribute(AttributeKey::COMMENTS, null);
        $secondNode->setAttribute(AttributeKey::COMMENTS, null);

        return $this->standard->prettyPrint([$firstNode]) === $this->standard->prettyPrint([$secondNode]);
    }
}
