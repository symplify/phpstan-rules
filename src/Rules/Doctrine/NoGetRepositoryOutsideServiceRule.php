<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\DoctrineRuleIdentifier;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOutsideServiceRule\NoGetRepositoryOutsideServiceRuleTest
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
            $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->identifier(DoctrineRuleIdentifier::NO_GET_REPOSITORY_OUTSIDE_SERVICE)
                ->build();

            return [$ruleError];
        }

        // dummy check
        $classReflection = $scope->getClassReflection();
        if (substr_compare($classReflection->getName(), 'Repository', -strlen('Repository')) === 0) {
            return [];
        }

        $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(DoctrineRuleIdentifier::NO_GET_REPOSITORY_OUTSIDE_SERVICE)
            ->build();

        return [$ruleError];
    }
}
