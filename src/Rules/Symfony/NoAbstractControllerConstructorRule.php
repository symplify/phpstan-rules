<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Check if abstract controller has constructor, as it should use
 * #[Require] instead to avoid parent constructor override
 *
 * @see \TomasVotruba\Handyman\Tests\PHPStan\Rule\NoAbstractControllerConstructorRule\NoAbstractControllerConstructorRuleTest
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
     * @return RuleError[]
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
        if (! str_ends_with($className, 'Controller')) {
            return [];
        }

        if (! $node->getMethod('__construct')) {
            return [];
        }

        $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)->build();
        return [$ruleError];
    }
}
