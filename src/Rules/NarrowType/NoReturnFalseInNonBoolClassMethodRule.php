<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\NarrowType;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\Constant\ConstantBooleanType;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @implements Rule<ClassMethod>
 */
final class NoReturnFalseInNonBoolClassMethodRule implements Rule, DocumentedRuleInterface
{
    /**
     * @api
     * @var string
     */
    public const ERROR_MESSAGE = 'Returning false in non return bool class method. Use null instead';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @retur string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->stmts === null) {
            return [];
        }

        if ($node->returnType instanceof Node) {
            return [];
        }

        $nodeFinder = new NodeFinder();

        /** @var Node\Stmt\Return_[] $returns */
        $returns = $nodeFinder->findInstanceOf($node->stmts, Node\Stmt\Return_::class);

        foreach ($returns as $return) {
            if (! $return->expr instanceof Node\Expr) {
                continue;
            }

            $exprType = $scope->getType($return->expr);
            if (! $exprType instanceof ConstantBooleanType) {
                continue;
            }

            if ($exprType->getValue() !== false) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @var Item[]
     */
    private $items = [];

    public function getItem($key)
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        return false;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @var Item[]
     */
    private $items = [];

    public function getItem($key): ?Item
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        return null;
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
