<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Rector\Rector\AbstractRector;
use Symplify\PHPStanRules\Enum\RuleIdentifier\RectorRuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Rector\NoOnlyNullReturnInRefactorRule\NoOnlyNullReturnInRefactorRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final class NoOnlyNullReturnInRefactorRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'The refactor() method returns always null, but it should return at least one modified node';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! NamingHelper::isName($node->name, 'refactor')) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! $classReflection->is(AbstractRector::class)) {
            return [];
        }

        $nodeFinder = new NodeFinder();
        $returns = $nodeFinder->findInstanceOf((array) $node->stmts, Return_::class);

        // should not happen, but out of scope of this PR
        if ($returns === []) {
            return [];
        }

        foreach ($returns as $return) {
            if (! $return->expr instanceof ConstFetch) {
                return [];
            }

            if ($return->expr->name->toLowerString() !== 'null') {
                return [];
            }
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RectorRuleIdentifier::NO_ONLY_NULL_RETURN_IN_REFACTOR)
            ->build()];
    }
}
