<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\RectorRuleIdentifier;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Rector\NoIntegerRefactorReturnRule\NoIntegerRefactorReturnRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final class NoIntegerRefactorReturnRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of using DONT_TRAVERSE_CHILDREN* or STOP_TRAVERSAL in refactor() method, make use of attributes. Return always node, null or REMOVE_NODE. Using traverser enums might lead to unexpected results';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->isPublic()) {
            return [];
        }

        if ($node->name->toString() !== 'refactor') {
            return [];
        }

        if (! $node->returnType instanceof UnionType) {
            return [];
        }

        foreach ($node->returnType->types as $type) {
            if (! $type instanceof Identifier) {
                continue;
            }

            if ($type->name !== 'int') {
                continue;
            }

            $constantNames = $this->findUsedNodeVisitorConstantNames($node);

            $undesiredConstantNames = array_diff($constantNames, ['REMOVE_NODE']);
            if ($undesiredConstantNames === []) {
                return [];
            }

            $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->identifier(RectorRuleIdentifier::NO_INTEGER_REFACTOR_RETURN)
                ->build();

            return [$ruleError];
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function findUsedNodeVisitorConstantNames(ClassMethod $classMethod): array
    {
        $constantNames = [];

        // find exact constants
        $nodeFinder = new NodeFinder();
        $nodeFinder->find($classMethod, function (Node $subNode) use (&$constantNames): int|bool {
            // skip closure nodes as they have their own scope
            if ($subNode instanceof Closure) {
                return NodeVisitor::DONT_TRAVERSE_CHILDREN;
            }

            if (! $subNode instanceof ClassConstFetch) {
                return false;
            }

            if (! $subNode->class instanceof Node\Name) {
                return false;
            }

            if (! in_array($subNode->class->toString(), [NodeVisitor::class, NodeTraverser::class])) {
                return false;
            }

            if (! $subNode->name instanceof Identifier) {
                return false;
            }

            $constantNames[] = $subNode->name->toString();
            return true;
        });

        return array_unique($constantNames);
    }
}
