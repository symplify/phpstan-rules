<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\NodeAnalyzer\ScalarValueResolver;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\NoDuplicatedArgumentRule\NoDuplicatedArgumentRuleTest
 */
final class NoDuplicatedArgumentRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'This call has duplicate argument';
    /**
     * @var \PHPStan\Reflection\ReflectionProvider
     */
    private $reflectionProvider;
    /**
     * @var \Symplify\PHPStanRules\NodeAnalyzer\ScalarValueResolver
     */
    private $scalarValueResolver;

    public function __construct(ReflectionProvider $reflectionProvider, ScalarValueResolver $scalarValueResolver)
    {
        $this->reflectionProvider = $reflectionProvider;
        $this->scalarValueResolver = $scalarValueResolver;
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return CallLike::class;
    }

    /**
     * @param CallLike $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node instanceof MethodCall && ! $node instanceof FuncCall && ! $node instanceof StaticCall) {
            return [];
        }

        if ($this->shouldSkip($node, $scope)) {
            return [];
        }

        $countValues = $this->scalarValueResolver->resolveValuesCountFromArgs($node->getArgs(), $scope);

        // each of kind
        if ($countValues === []) {
            return [];
        }

        $maxCountValues = max($countValues);
        if ($maxCountValues === 1) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            self::ERROR_MESSAGE,
            [new CodeSample(
                <<<'CODE_SAMPLE'
function run($one, $one);
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
function run($one, $two);
CODE_SAMPLE
            )]
        );
    }

    /**
     * @param \PhpParser\Node\Expr\StaticCall|\PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\FuncCall $expr
     */
    private function shouldSkip($expr, Scope $scope): bool
    {
        if (\count($expr->args) < 2) {
            return true;
        }

        if ($expr instanceof FuncCall) {
            if (! $expr->name instanceof Name) {
                return true;
            }

            if (! $this->reflectionProvider->hasFunction($expr->name, $scope)) {
                return false;
            }

            // skip native functions
            $functionReflection = $this->reflectionProvider->getFunction($expr->name, $scope);
            return $functionReflection->isBuiltin();
        }

        return false;
    }
}
