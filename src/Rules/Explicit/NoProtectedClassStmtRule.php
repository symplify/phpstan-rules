<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\MethodName;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<InClassNode>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoProtectedClassStmtRule\NoProtectedClassStmtRuleTest
 */
final class NoProtectedClassStmtRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid protected class stmts as they yield unexpected behavior. Use clear interface contract instead';

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        // skip abstract classes for onw
        if ($classLike->isAbstract()) {
            return [];
        }

        $ruleErrors = [];

        foreach ($classLike->stmts as $classStmt) {
            if (! $classStmt instanceof ClassMethod && ! $classStmt instanceof ClassConst && ! $classStmt instanceof Property) {
                continue;
            }

            // skip test one
            if ($this->shouldSkipClassMethod($classStmt, $scope)) {
                continue;
            }

            if (! $classStmt->isProtected()) {
                continue;
            }

            $ruleErrors[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->line($classStmt->getStartLine())
                ->identifier(RuleIdentifier::NO_PROTECTED_CLASS_STMT)
                ->build();
        }

        return $ruleErrors;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\ClassConst|\PhpParser\Node\Stmt\Property $classStmt
     */
    private function shouldSkipClassMethod($classStmt, Scope $scope): bool
    {
        if (! $classStmt instanceof ClassMethod) {
            return false;
        }

        // PHPUnit test methods
        if (in_array($classStmt->name->toString(), [MethodName::SET_UP, MethodName::TEAR_DOWN])) {
            return true;
        }

        return $this->isParentMethodOverride($classStmt, $scope);
    }

    private function isParentMethodOverride(ClassMethod $classMethod, Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        $parentClassReflection = $classReflection->getParentClass();
        if (! $parentClassReflection instanceof ClassReflection) {
            return false;
        }

        return $parentClassReflection->hasMethod($classMethod->name->toString());
    }
}
