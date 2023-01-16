<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDocParser\PhpDocNodeVisitor;

use PHPStan\PhpDocParser\Ast\Node;

final class CallablePhpDocNodeVisitor extends AbstractPhpDocNodeVisitor
{
    /**
     * @var callable(Node, string|null): (int|null|Node)
     */
    private $callable;

    /**
     * @param callable(Node $callable, string|null $docContent): (int|null|Node) $callable
     */
    public function __construct(
        callable $callable,
        private readonly ?string $docContent
    ) {
        $this->callable = $callable;
    }

    public function enterNode(Node $node): int|Node|null
    {
        $callable = $this->callable;
        return $callable($node, $this->docContent);
    }
}
