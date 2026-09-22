<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\ConfigClosure;

use PhpParser\Node;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr;
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
 *
 * or a single brace-glob string:
 *
 * $services->load('...', '...')
 *     ->exclude('../{X,Y}');
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

            $excludedExpr = $node->getArgs()[0]
                ->value;

            $pathExprs = $excludedExpr instanceof Array_
                ? array_map(static fn (ArrayItem $arrayItem): Expr => $arrayItem->value, array_filter($excludedExpr->items))
                : [$excludedExpr];

            foreach ($pathExprs as $pathExpr) {
                foreach (self::resolvePathPatterns($pathExpr, $scope) as $pathPattern) {
                    foreach (self::expandBraces($pathPattern) as $expandedPath) {
                        $realExcludedPath = realpath($expandedPath);
                        if (is_string($realExcludedPath)) {
                            $excludedPaths[] = $realExcludedPath;
                        }
                    }
                }
            }

            return true;
        });

        return array_unique($excludedPaths);
    }

    /**
     * Resolve an exclude path expression to absolute path patterns (braces still unexpanded).
     *
     * @return string[]
     */
    private static function resolvePathPatterns(Expr $expr, Scope $scope): array
    {
        $pathPatterns = [];

        // known constant string, e.g. brace glob "../{X,Y}" built from consts + implode()
        foreach ($scope->getType($expr)->getConstantStrings() as $constantStringType) {
            $value = $constantStringType->getValue();

            // absolute path is used as-is, relative path is resolved against the config file directory
            $pathPatterns[] = str_starts_with($value, '/')
                ? $value
                : dirname($scope->getFile()) . '/' . $value;
        }

        if ($pathPatterns !== []) {
            return $pathPatterns;
        }

        // fallback for a "__DIR__ . '/relative'" concat that stays a non-constant literal-string
        if ($expr instanceof Concat && $expr->right instanceof String_) {
            $pathPatterns[] = dirname($scope->getFile()) . $expr->right->value;
        }

        return $pathPatterns;
    }

    /**
     * Expand "a/{b,c}" glob into ["a/b", "a/c"].
     *
     * @return string[]
     */
    private static function expandBraces(string $pattern): array
    {
        if (! preg_match('/\{([^{}]*)\}/', $pattern, $match, PREG_OFFSET_CAPTURE)) {
            return [$pattern];
        }

        $prefix = substr($pattern, 0, (int) $match[0][1]);
        $suffix = substr($pattern, (int) $match[0][1] + strlen((string) $match[0][0]));

        $expanded = [];
        foreach (explode(',', (string) $match[1][0]) as $option) {
            foreach (self::expandBraces($prefix . $option . $suffix) as $expandedPattern) {
                $expanded[] = $expandedPattern;
            }
        }

        return $expanded;
    }

    private static function isName(Node $node, string $name): bool
    {
        if (! $node instanceof Name && ! $node instanceof Identifier) {
            return false;
        }

        return $node->toString() === $name;
    }
}
