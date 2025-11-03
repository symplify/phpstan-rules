<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Enum\SymfonyClass;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\NoControllerMethodInjectionRule\NoControllerMethodInjectionRuleTest
 *
 * @implements Rule<Class_>
 */
final class NoControllerMethodInjectionRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of service "%s" action injection, use __construct() and invokable controller with __invoke() to clearly separate services and parameters';

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $ruleErrors = [];

        if (! $node->name instanceof Identifier) {
            return [];
        }

        $className = $node->name->toString();

        if (substr_compare($className, 'Controller', -strlen('Controller')) !== 0) {
            return [];
        }

        foreach ($node->getMethods() as $classMethod) {
            if (! $classMethod->isPublic()) {
                continue;
            }

            if ($classMethod->isMagic()) {
                continue;
            }

            if ($classMethod->getParams() === []) {
                continue;
            }

            foreach ($classMethod->getParams() as $param) {
                if (! $param->type instanceof Name) {
                    continue;
                }

                // Request is allwoed
                $typeName = $param->type->toString();
                if ($typeName === SymfonyClass::REQUEST) {
                    continue;
                }

                $identifierRuleError = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $typeName))
                    ->identifier(SymfonyRuleIdentifier::NO_CONTROLLER_METHOD_INJECTION)
                    ->line($classMethod->getStartLine())
                    ->build();

                $ruleErrors[] = $identifierRuleError;
            }
        }

        return $ruleErrors;
    }
}
