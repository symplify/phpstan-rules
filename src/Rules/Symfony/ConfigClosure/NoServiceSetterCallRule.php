<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony\ConfigClosure;

use Entropy\Utils\Regex;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * A config-closure setter injection wired by hand - $services->get(X::class)->call('setFoo', [service(Foo::class)]) -
 * should be a #[Required] attribute on the setter instead, so autowiring calls it and the config no longer names the
 * method by a loose string.
 *
 * Only a call() naming a setXxx() method that a service() feeds is reported. A call() to another method runs logic
 * the container cannot infer, and a setter fed a container parameter has no type to autowire, so both stay a manual call.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoServiceSetterCallRule\NoServiceSetterCallRuleTest
 *
 * @implements Rule<MethodCall>
 */
final class NoServiceSetterCallRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Setter call() to "%s()" wires the dependency by hand, mark the method #[Required] and let autowiring call it instead';

    /**
     * A setter is a setXxx() method, e.g. setListLeadRepository(). A "setup" or "settle" method is no setter.
     * @var string
     */
    private const SETTER_METHOD_PATTERN = '#^set\p{Lu}#u';

    /**
     * @var string
     */
    private const SERVICE_FUNCTION = 'Symfony\Component\DependencyInjection\Loader\Configurator\service';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     *
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier || $node->name->toString() !== 'call') {
            return [];
        }

        $firstArg = $node->getArgs()[0] ?? null;
        if ($firstArg === null || ! $firstArg->value instanceof String_) {
            return [];
        }

        $methodName = $firstArg->value->value;
        if (Regex::match($methodName, self::SETTER_METHOD_PATTERN) === []) {
            return [];
        }

        // only a setter fed a service() can move to #[Required]; a container parameter is not resolved by type
        if (! $this->hasServiceArgument($node)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $methodName))
                ->identifier('symfony.noServiceSetterCall')
                ->line($node->getStartLine())
                ->build(),
        ];
    }

    /**
     * The arguments of a call() travel in an array, e.g. ->call('setFieldModel', [service(FieldModel::class)]).
     * A service() reference among them is the dependency #[Required] autowiring resolves by type.
     */
    private function hasServiceArgument(MethodCall $methodCall): bool
    {
        $secondArg = $methodCall->getArgs()[1] ?? null;
        if ($secondArg === null || ! $secondArg->value instanceof Array_) {
            return false;
        }

        $found = false;
        foreach ($secondArg->value->items as $arrayItem) {
            if ($arrayItem->value instanceof FuncCall && $this->isServiceFunction($arrayItem->value)) {
                $found = true;
                break;
            }
        }

        return $found;
    }

    private function isServiceFunction(FuncCall $funcCall): bool
    {
        if (! $funcCall->name instanceof Name) {
            return false;
        }

        $functionName = $funcCall->name->toString();

        return $functionName === 'service' || $functionName === self::SERVICE_FUNCTION;
    }
}
