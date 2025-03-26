<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\ConfigClosure;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;

/**
 * Look for:
 *
 * $services->load('...', '...')
 *     ->exclude(['X', 'Y']);
 */
final class SymfonyClosureServicesExcludeResolver
{
    /**
     * @return string[]
     */
    public static function resolve(Closure $closure, Scope $scope): array
    {
        $excludedPaths = [];

        $nodeFinder = new NodeFinder();
        $nodeFinder->find($closure->stmts, function (Node $node) use (&$excludedPaths, $scope): bool {
            if (! $node instanceof MethodCall) {
                return false;
            }

            if (! self::isName($node->name, 'exclude')) {
                return false;
            }

            $excludedExpr = $node->getArgs()[0]->value;
            if (! $excludedExpr instanceof Array_) {
                return false;
            }

            foreach ($excludedExpr->items as $arrayItem) {
                if (! $arrayItem->value instanceof Concat) {
                    continue;
                }

                $concat = $arrayItem->value;
                if (! $concat->right instanceof String_) {
                    continue;
                }

                $excludedPath = dirname($scope->getFile()) . $concat->right->value;
                $realExcludedPath = realpath($excludedPath);
                if (! is_string($realExcludedPath)) {
                    continue;
                }

                $excludedPaths[] = $realExcludedPath;
            }

            return true;
        });

        return array_unique($excludedPaths);
    }

    private static function isName(Node $node, string $name): bool
    {
        if (! $node instanceof Name && ! $node instanceof Identifier) {
            return false;
        }

        return $node->toString() === $name;
    }
}
