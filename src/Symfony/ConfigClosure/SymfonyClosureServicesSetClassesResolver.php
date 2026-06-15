<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\ConfigClosure;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;

/**
 * Look for:
 *
 * $services->set(X);
 */
final class SymfonyClosureServicesSetClassesResolver
{
    /**
     * @return array<string, int>
     */
    public static function resolve(Closure $closure): array
    {
        $standaloneSetServices = [];

        $nodeFinder = new NodeFinder();
        $nodeFinder->find($closure, function (Node $node) use (&$standaloneSetServices): bool {
            if (! $node instanceof Expression) {
                return false;
            }

            if (! $node->expr instanceof MethodCall) {
                return false;
            }

            $methodCall = $node->expr;
            if (! $methodCall->var instanceof Variable) {
                return false;
            }

            // dummy services check, to avoid collecting parameters
            if (! self::isName($methodCall->var->name, 'services')) {
                return false;
            }

            if (! self::isName($methodCall->name, 'set')) {
                return false;
            }

            $setServiceExpr = $methodCall->getArgs()[0]->value;
            if (! $setServiceExpr instanceof ClassConstFetch) {
                return false;
            }

            if (! $setServiceExpr->class instanceof Name) {
                return false;
            }

            $serviceClass = $setServiceExpr->class->toString();
            $standaloneSetServices[$serviceClass] = $setServiceExpr->getStartLine();

            return true;
        });

        return $standaloneSetServices;
    }

    /**
     * @param \PhpParser\Node|string $node
     */
    private static function isName($node, string $name): bool
    {
        if (is_string($node)) {
            return $node === $name;
        }

        if (! $node instanceof Name && ! $node instanceof Identifier) {
            return false;
        }

        return $node->toString() === $name;
    }
}
