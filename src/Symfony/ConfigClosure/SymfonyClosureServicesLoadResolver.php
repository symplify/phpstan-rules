<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\ConfigClosure;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;

/**
 * Look for:
 *
 * $services->load('Y')
 */
final class SymfonyClosureServicesLoadResolver
{
    /**
     * @return string[]
     */
    public static function resolve(Closure $closure): array
    {
        $loadedNamespaces = [];

        $nodeFinder = new NodeFinder();
        $nodeFinder->find($closure->stmts, function (Node $node) use (&$loadedNamespaces): bool {
            if (! $node instanceof MethodCall) {
                return false;
            }

            if (! self::isName($node->name, 'load')) {
                return false;
            }

            $namespaceExpr = $node->getArgs()[0]->value;
            if (! $namespaceExpr instanceof String_) {
                return false;
            }

            $loadedNamespaces[] = $namespaceExpr->value;
            return true;
        });

        return $loadedNamespaces;
    }

    private static function isName(Node $node, string $name): bool
    {
        if (! $node instanceof Name && ! $node instanceof Identifier) {
            return false;
        }

        return $node->toString() === $name;
    }
}
