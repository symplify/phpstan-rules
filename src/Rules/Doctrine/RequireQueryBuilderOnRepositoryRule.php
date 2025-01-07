<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use Symplify\PHPStanRules\Enum\ClassName;
use Symplify\PHPStanRules\Enum\DoctrineRuleIdentifier;

/**
 * @implements Rule<MethodCall>
 */
final class RequireQueryBuilderOnRepositoryRule implements Rule
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Avoid calling ->createQueryBuilder() directly on EntityManager as it requires select() + from() calls with specific values. Use $repository->createQueryBuilder() to be safe instead';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier) {
            return [];
        }

        if ($node->name->toString() !== 'createQueryBuilder') {
            return [];
        }

        $callerType = $scope->getType($node->var);
        if (! $callerType instanceof ObjectType) {
            return [];
        }

        // we safe as both select() + from() calls are made on the repository
        if ($callerType->isInstanceOf(ClassName::ENTITY_REPOSITORY_CLASS)->yes()) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(DoctrineRuleIdentifier::REQUIRE_QUERY_BUILDER_ON_REPOSITORY)
            ->build();

        return [$identifierRuleError];
    }
}
