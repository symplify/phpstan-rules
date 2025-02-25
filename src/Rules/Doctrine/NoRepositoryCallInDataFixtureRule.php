<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\DoctrineClass;
use Symplify\PHPStanRules\Enum\DoctrineRuleIdentifier;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\NoRepositoryCallInDataFixtureRule\NoRepositoryCallInDataFixtureRuleTest;

/**
 * @see NoRepositoryCallInDataFixtureRuleTest
 *
 * @implements Rule<MethodCall>
 */
final class NoRepositoryCallInDataFixtureRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Refactor read-data fixtures to write-only, make use of references';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return IdentifierRuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->isFirstClassCallable()) {
            return [];
        }

        if (! $this->isDataFixtureClass($scope)) {
            return [];
        }

        if (! $node->name instanceof Identifier) {
            return [];
        }

        $methodName = $node->name->toString();
        if (! in_array($methodName, ['getRepository', 'find', 'findAll', 'findBy', 'findOneBy'])) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(DoctrineRuleIdentifier::NO_REPOSITORY_CALL_IN_DATA_FIXTURES)
            ->build();

        return [$identifierRuleError];
    }

    private function isDataFixtureClass(Scope $scope): bool
    {
        if (! $scope->isInClass()) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        return $classReflection->isSubclassOf(DoctrineClass::FIXTURE_INTERFACE);
    }
}
