<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoInstanceOfStaticReflectionRule\Fixture;

use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;

final class SkipPhpDocNode
{
    /**
     * @param class-string<IdentifierTypeNode> $type
     */
    public function find(object $node, $type): bool
    {
        return is_a($node, $type, true);
    }
}
