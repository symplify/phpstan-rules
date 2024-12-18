<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Check if class extends repository class,
 * the entity manager should be injected via constructor instead
 *
 * @see \TomasVotruba\Handyman\Tests\PHPStan\Rule\NoParentRepositoryRule\NoParentRepositoryRuleTest
 *
 * @implements Rule<Class_>
 */
final class NoParentRepositoryRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Extending EntityRepository is not allowed, use constructor injection and pass entity manager instead';

    /**
     * @var string
     */
    private const ENTITY_REPOSITORY_CLASS = 'Doctrine\ORM\EntityRepository';

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
        if (! $node->extends instanceof Name) {
            return [];
        }

        $parentClass = $node->extends->toString();
        if ($parentClass !== self::ENTITY_REPOSITORY_CLASS) {
            return [];
        }

        $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->build();

        return [$ruleError];
    }
}
