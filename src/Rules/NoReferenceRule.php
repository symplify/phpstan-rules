<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\ParentClassMethodNodeResolver;
use function array_map;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoReferenceRule\NoReferenceRuleTest
 */
final class NoReferenceRule extends AbstractSymplifyRule
{
    /**
     * @readonly
     */
    private ParentClassMethodNodeResolver $parentClassMethodNodeResolver;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit return value over magic &reference';

    public function __construct(ParentClassMethodNodeResolver $parentClassMethodNodeResolver)
    {
        $this->parentClassMethodNodeResolver = $parentClassMethodNodeResolver;
    }

    public function getNodeTypes(): array
    {
        return [
            ClassMethod::class,
            Function_::class,
            AssignRef::class,
            Arg::class,
            Foreach_::class,
            ArrayItem::class,
            ArrowFunction::class,
            Closure::class,
        ];
    }

    /**
     * @param ClassMethod|Function_|AssignRef|Arg|Foreach_|ArrayItem|ArrowFunction|Closure $node
     */
    public function process(Node $node, Scope $scope): array
    {
        $errorMessages = [];

        if ($node instanceof AssignRef) {
            $errorMessages[] = self::ERROR_MESSAGE;
        } elseif ($node->byRef) {
            $errorMessages[] = self::ERROR_MESSAGE;
        }

        $paramErrorMessage = $this->collectParamErrorMessages($node, $scope);
        $errorMessages = array_merge($errorMessages, $paramErrorMessage);

        return array_map(
            static fn ($message): RuleError => RuleErrorBuilder::message($message)->build(),
            array_unique($errorMessages),
        );
    }

    /**
     * @return string[]
     */
    private function collectParamErrorMessages(Node $node, Scope $scope): array
    {
        if (! $node instanceof Function_ && ! $node instanceof ClassMethod) {
            return [];
        }

        // has parent method? → skip it as enforced by parent
        $methodName = (string) $node->name;

        $parentClassMethod = $this->parentClassMethodNodeResolver->resolveParentClassMethod($scope, $methodName);
        if ($parentClassMethod instanceof ClassMethod) {
            return [];
        }

        $errorMessages = [];
        foreach ($node->params as $param) {
            /** @var Param $param */
            if (! $param->byRef) {
                continue;
            }

            $errorMessages[] = self::ERROR_MESSAGE;
        }

        return $errorMessages;
    }
}
