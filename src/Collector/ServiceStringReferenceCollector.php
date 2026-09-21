<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * Collects the config-closure references that ask for a service by a string id,
 * e.g. service('some.helper').
 *
 * A service(SomeHelper::class) reference is left out, that one already names the service by its class.
 *
 * @implements Collector<FuncCall, array{string, int}>
 */
final class ServiceStringReferenceCollector implements Collector
{
    private const string SERVICE_FUNCTION = 'Symfony\Component\DependencyInjection\Loader\Configurator\service';

    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * @return array{string, int}|null the string service id with the line the reference is on
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (! $node->name instanceof Name) {
            return null;
        }

        $functionName = $node->name->toString();
        if ($functionName !== 'service' && $functionName !== self::SERVICE_FUNCTION) {
            return null;
        }

        $firstArg = $node->getArgs()[0] ?? null;
        if (! $firstArg instanceof Arg || ! $firstArg->value instanceof String_) {
            return null;
        }

        return [$firstArg->value->value, $node->getStartLine()];
    }
}
