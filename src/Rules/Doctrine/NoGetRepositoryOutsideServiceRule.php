<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Check if abstract controller has constructor, as it should use
 * #[Require] instead to avoid parent constructor override
 *
 * @see \TomasVotruba\Handyman\Tests\PHPStan\Rule\NoGetRepositoryOutsideServiceRule\NoGetRepositoryOutsideServiceRuleTest
 *
 * @implements Rule<MethodCall>
 */
final class NoGetRepositoryOutsideServiceRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of getting repository from EntityManager, use constructor injection and service pattern to keep code clean';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier) {
            return [];
        }

        if ($node->name->toString() !== 'getRepository') {
            return [];
        }

        if (! $scope->isInClass()) {
            $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)->build();
            return [$ruleError];
        }

        // dummy check
        $classReflection = $scope->getClassReflection();
        if (str_ends_with($classReflection->getName(), 'Repository')) {
            return [];
        }

        $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)->build();
        return [$ruleError];
    }
}
