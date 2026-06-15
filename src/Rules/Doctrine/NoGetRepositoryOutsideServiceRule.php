<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\DoctrineRuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;

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
        if ($node->isFirstClassCallable()) {
            return [];
        }

        if (! NamingHelper::isName($node->name, 'getRepository')) {
            return [];
        }

        if ($this->isDynamicArg($node)) {
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

    private function isDynamicArg(MethodCall $methodCall): bool
    {
        $firstArg = $methodCall->getArgs()[0];
        if ($firstArg->value instanceof String_) {
            return false;
        }

        if ($firstArg->value instanceof ClassConstFetch) {
            $classConstFetch = $firstArg->value;
            return ! $classConstFetch->class instanceof Name;
        }

        return true;
    }
}
