<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;

/**
 * Check if abstract controller has constructor, as it should use
 * #[Require] instead to avoid parent constructor override
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\NoAbstractControllerConstructorRule\NoAbstractControllerConstructorRuleTest
 *
 * @implements Rule<Class_>
 */
final class NoAbstractControllerConstructorRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Abstract controller should not have constructor, to avoid override by child classes. Use #[Require] or @require and autowire() method instead';

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->isAbstract()) {
            return [];
        }

        if (! $node->name instanceof Identifier) {
            return [];
        }

        $className = $node->name->toString();
        if (substr_compare($className, 'Controller', -strlen('Controller')) !== 0) {
            return [];
        }

        if (! $node->getMethod('__construct')) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(SymfonyRuleIdentifier::SYMFONY_NO_ABSTRACT_CONTROLLER_CONSTRUCTOR)
            ->build();

        return [$identifierRuleError];
    }
}
