<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;

/**
 * A container definition fetch that names a class by a plain string should use the class constant instead, e.g.
 * $container->getDefinition('App\Helper\ColumnSchemaHelper') should pass ColumnSchemaHelper::class. The string is
 * only flagged when it is a real class name, a service id string such as 'app.helper.core' is left alone.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\PreferClassInDefinitionFetchRule\PreferClassInDefinitionFetchRuleTest
 *
 * @implements Rule<MethodCall>
 */
final readonly class PreferClassInDefinitionFetchRule implements Rule
{
    public const string ERROR_MESSAGE = 'Fetch the definition by class constant, %s::class, rather than the string "%s"';

    /**
     * @var list<string>
     */
    private const array DEFINITION_METHOD_NAMES = ['getDefinition', 'hasDefinition', 'findDefinition', 'removeDefinition'];

    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {
    }

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
        if (! $node->name instanceof Identifier || ! in_array($node->name->toString(), self::DEFINITION_METHOD_NAMES, true)) {
            return [];
        }

        $firstArg = $node->getArgs()[0] ?? null;
        if (! $firstArg instanceof Arg || ! $firstArg->value instanceof String_) {
            return [];
        }

        $className = $firstArg->value->value;
        if (! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $ruleError = RuleErrorBuilder::message(
            sprintf(self::ERROR_MESSAGE, $className, $className)
        )
            ->identifier(SymfonyRuleIdentifier::PREFER_CLASS_IN_DEFINITION_FETCH)
            ->line($firstArg->getStartLine())
            ->build();

        return [$ruleError];
    }
}
