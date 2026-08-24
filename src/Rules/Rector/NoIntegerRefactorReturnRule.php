<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\RectorRuleIdentifier;
use Symplify\PHPStanRules\NodeTraverser\SimpleCallableNodeTraverser;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Rector\NoIntegerRefactorReturnRule\NoIntegerRefactorReturnRuleTest
 *
 * @implements Rule<Class_>
 */
final class NoIntegerRefactorReturnRule implements Rule
{
    public const string ERROR_MESSAGE = 'Instead of using DONT_TRAVERSE_CHILDREN* or STOP_TRAVERSAL in refactor() method, make use of attributes. Return always node, null or REMOVE_NODE. Using traverser enums might lead to unexpected results';

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $refactorClassMethod = $node->getMethod('refactor');
        if (! $refactorClassMethod instanceof ClassMethod) {
            return [];
        }

        if (! $refactorClassMethod->isPublic()) {
            return [];
        }

        if (! $this->hasIntReturnType($refactorClassMethod->returnType)) {
            return [];
        }

        // scan the whole class, as refactor() often delegates the int return to private helper methods
        $constantNames = $this->findUsedNodeVisitorConstantNames($node);

        $undesiredConstantNames = array_diff($constantNames, ['REMOVE_NODE']);
        if ($undesiredConstantNames === []) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RectorRuleIdentifier::NO_INTEGER_REFACTOR_RETURN)
            ->line($refactorClassMethod->getStartLine())
            ->build();

        return [$identifierRuleError];
    }

    private function hasIntReturnType(?Node $node): bool
    {
        // bare "int" return type
        if ($node instanceof Identifier) {
            return $node->name === 'int';
        }

        // "int" as one of the union members
        if ($node instanceof UnionType) {
            foreach ($node->types as $type) {
                if ($type instanceof Identifier && $type->name === 'int') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    private function findUsedNodeVisitorConstantNames(Class_ $class): array
    {
        $constantNames = [];

        $simpleCallableNodeTraverser = new SimpleCallableNodeTraverser();
        $simpleCallableNodeTraverser->traverseNodesWithCallable($class, function (Node $subNode) use (&$constantNames): int|null {
            // skip closure nodes as they have their own scope
            if ($subNode instanceof Closure) {
                return NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }

            if (! $subNode instanceof ClassConstFetch) {
                return null;
            }

            if (! $subNode->class instanceof Name) {
                return null;
            }

            if (! in_array($subNode->class->toString(), [NodeVisitor::class, NodeTraverser::class], true)) {
                return null;
            }

            if (! $subNode->name instanceof Identifier) {
                return null;
            }

            $constantNames[] = $subNode->name->toString();
            return null;
        });

        return array_unique($constantNames);
    }
}
