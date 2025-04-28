<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use Symplify\PHPStanRules\Enum\DoctrineClass;
use Symplify\PHPStanRules\Enum\RuleIdentifier\DoctrineRuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;

/**
 * @implements Rule<MethodCall>
 * @see \Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\RequireQueryBuilderOnRepositoryRuleTest
 */
final class RequireQueryBuilderOnRepositoryRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid calling ->createQueryBuilder() directly on EntityManager as it requires select() + from() calls with specific values. Use $repository->createQueryBuilder() to be safe instead';

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
        if ($this->isValidRepositoryObjectType($callerType)) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(DoctrineRuleIdentifier::REQUIRE_QUERY_BUILDER_ON_REPOSITORY)
            ->build();

        return [$identifierRuleError];
    }

    private function isValidRepositoryObjectType(Type $type): bool
    {
        if ($type instanceof UnionType) {
            foreach ($type->getTypes() as $unionType) {
                if ($this->isValidRepositoryObjectType($unionType)) {
                    return true;
                }
            }
        }

        if (! $type instanceof ObjectType) {
            return true;
        }

        // we safe as both select() + from() calls are made on the repository
        if ($type->isInstanceOf(DoctrineClass::ENTITY_REPOSITORY)->yes()) {
            return true;
        }

        if ($type->isInstanceOf(DoctrineClass::DOCUMENT_REPOSITORY)->yes()) {
            return true;
        }

        return $type->isInstanceOf(DoctrineClass::CONNECTION)->yes();
    }
}
