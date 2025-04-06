<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use Symplify\PHPStanRules\Enum\DoctrineClass;
use Symplify\PHPStanRules\Enum\RuleIdentifier\DoctrineRuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;

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
        if (! NamingHelper::isName($node->name, 'createQueryBuilder')) {
            return [];
        }

        $callerType = $scope->getType($node->var);
        if (! $callerType instanceof ObjectType) {
            return [];
        }

        // we safe as both select() + from() calls are made on the repository
        if ($callerType->isInstanceOf(DoctrineClass::ENTITY_REPOSITORY)->yes()) {
            return [];
        }

        if ($callerType->isInstanceOf(DoctrineClass::CONNECTION)->yes()) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(DoctrineRuleIdentifier::REQUIRE_QUERY_BUILDER_ON_REPOSITORY)
            ->build();

        return [$identifierRuleError];
    }
}
