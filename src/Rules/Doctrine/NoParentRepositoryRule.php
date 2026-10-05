<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\DoctrineClass;
use Symplify\PHPStanRules\Enum\RuleIdentifier\DoctrineRuleIdentifier;

/**
 * Check if class extends repository class,
 * the entity manager should be injected via constructor instead
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Doctrine\NoParentRepositoryRule\NoParentRepositoryRuleTest
 *
 * @implements Rule<Class_>
 */
final class NoParentRepositoryRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Extending EntityRepository is not allowed, use constructor injection and pass entity manager instead';

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->extends instanceof Name) {
            return [];
        }

        $parentClass = $node->extends->toString();
        if ($parentClass !== DoctrineClass::ENTITY_REPOSITORY) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(DoctrineRuleIdentifier::NO_PARENT_REPOSITORY)
            ->build();

        return [$identifierRuleError];
    }
}
