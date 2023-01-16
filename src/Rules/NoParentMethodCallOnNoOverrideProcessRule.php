<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Printer\NodeComparator;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\NoParentMethodCallOnNoOverrideProcessRuleTest
 */
final class NoParentMethodCallOnNoOverrideProcessRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not call parent method if no override process';

    public function __construct(
        private readonly NodeComparator $nodeComparator
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $onlyNode = $this->resolveOnlyNode($node);
        if (! $onlyNode instanceof StaticCall) {
            return [];
        }

        if (! $this->isParentSelfMethodStaticCall($onlyNode, $node)) {
            return [];
        }

        $methodCallArgs = $onlyNode->args;
        $classMethodParams = $node->params;

        if (! $this->nodeComparator->areArgsAndParamsSame($methodCallArgs, $classMethodParams)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass extends Printer
{
    public function print($nodes)
    {
        return parent::print($nodes);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass extends Printer
{
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isParentSelfMethodStaticCall(StaticCall $staticCall, ClassMethod $classMethod): bool
    {
        if (! $staticCall->class instanceof Name) {
            return false;
        }

        if ($staticCall->class->toString() !== 'parent') {
            return false;
        }

        if (! $staticCall->name instanceof Identifier) {
            return false;
        }

        return $staticCall->name->toString() === $classMethod->name->toString();
    }

    private function resolveOnlyNode(ClassMethod $classMethod): ?Node
    {
        $stmts = (array) $classMethod->stmts;
        if (count($stmts) !== 1) {
            return null;
        }

        $onlyStmt = $stmts[0];
        if (! $onlyStmt instanceof Expression) {
            return null;
        }

        return $onlyStmt->expr;
    }
}
