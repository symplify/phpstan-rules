<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use Symplify\PHPStanRules\Symfony\ConfigClosure\SymfonyServiceReferenceFunctionAnalyzer;

final class RepeatedServiceAdderCallNameFinder
{
    private const CALL_NAME = 'call';

    private const MIN_ALERT_COUNT = 3;

    public static function find(MethodCall $methodCall): ?string
    {
        $callMethodCalls = self::findCallMethodCalls($methodCall);

        $callMethodNames = [];

        foreach ($callMethodCalls as $callMethodCall) {
            /** @var String_ $calledMethodNameExpr */
            $calledMethodNameExpr = $callMethodCall->getArgs()[0]->value;
            $callMethodName = $calledMethodNameExpr->value;

            // is passing a service references?
            $passedExpr = $callMethodCall->getArgs()[1]->value;
            if (! $passedExpr instanceof Array_) {
                continue;
            }

            if (count($passedExpr->items) !== 1) {
                continue;
            }

            $firstArrayItem = $passedExpr->items[0];
            if (! SymfonyServiceReferenceFunctionAnalyzer::isReferenceCall($firstArrayItem->value)) {
                continue;
            }

            $callMethodNames[] = $callMethodName;
        }

        $methodNamesToCount = array_count_values($callMethodNames);
        foreach ($methodNamesToCount as $methodName => $count) {
            if ($count < self::MIN_ALERT_COUNT) {
                continue;
            }

            return $methodName;
        }

        return null;
    }

    /**
     * @return MethodCall[]
     */
    private static function findCallMethodCalls(MethodCall $methodCall): array
    {
        $nodeFinder = new NodeFinder();

        /** @var MethodCall[] $callMethodCalls */
        $callMethodCalls = $nodeFinder->find($methodCall, function (Node $node): bool {
            if (! $node instanceof MethodCall) {
                return false;
            }

            if (! fast_node_named($node->name, self::CALL_NAME)) {
                return false;
            }

            if (count($node->getArgs()) !== 2) {
                return false;
            }

            $callNameExpr = $node->getArgs()[0]->value;
            return $callNameExpr instanceof String_;
        });

        return $callMethodCalls;
    }
}
