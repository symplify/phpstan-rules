<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\ParentClassMethodNodeResolver;
use Symplify\PHPStanRules\Testing\StaticPHPUnitEnvironment;

/**
 * @implements Rule<Return_>
 * @see \Symplify\PHPStanRules\Tests\Rules\NoReturnArrayVariableListRule\NoReturnArrayVariableListRuleTest
 */
final class NoReturnArrayVariableListRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use value object over return of values';

    /**
     * @var string
     * @see https://regex101.com/r/C5d1zH/1
     */
    private const TESTS_DIRECTORY_REGEX = '#\/Tests\/#i';

    public function __construct(
        private readonly ParentClassMethodNodeResolver $parentClassMethodNodeResolver,
    ) {
    }

    public function getNodeType(): string
    {
        return Return_::class;
    }

    /**
     * @param Return_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->shouldSkip($scope, $node)) {
            return [];
        }

        /** @var Array_ $array */
        $array = $node->expr;

        $itemCount = count($array->items);
        if ($itemCount < 2) {
            return [];
        }

        $exprCount = $this->resolveExprCount($array);
        if ($exprCount < 2) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)->build()];
    }

    private function shouldSkip(Scope $scope, Return_ $return): bool
    {
        // skip tests
        if (Strings::match(
            $scope->getFile(),
            self::TESTS_DIRECTORY_REGEX
        ) && ! StaticPHPUnitEnvironment::isPHPUnitRun()) {
            return true;
        }

        $namespace = $scope->getNamespace();
        if ($namespace === null) {
            return true;
        }

        if (str_contains($namespace, 'Enum')) {
            return true;
        }

        if (str_contains($namespace, 'ValueObject')) {
            return true;
        }

        if (! $return->expr instanceof Array_) {
            return true;
        }

        // guarded by parent method

        $functionLike = $scope->getFunction();
        if ($functionLike instanceof MethodReflection) {
            $parentClassMethod = $this->parentClassMethodNodeResolver->resolveParentClassMethod(
                $scope,
                $functionLike->getName()
            );

            return $parentClassMethod instanceof ClassMethod;
        }

        return false;
    }

    private function resolveExprCount(Array_ $array): int
    {
        $exprCount = 0;
        foreach ($array->items as $item) {
            if (! $item instanceof ArrayItem) {
                continue;
            }

            if ($item->value instanceof New_) {
                continue;
            }

            if ($item->unpack) {
                continue;
            }

            ++$exprCount;
        }

        return $exprCount;
    }
}
