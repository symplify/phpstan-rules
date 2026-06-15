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
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Symplify\PHPStanRules\ParentClassMethodNodeResolver;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoReferenceRule\NoReferenceRuleTest
 *
 * @implements Rule<Node>
 */
final class NoReferenceRule implements Rule
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

    public function getNodeType(): string
    {
        return Node::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $errorMessages = [];

        if ($node instanceof AssignRef) {
            return [$this->createRuleError()];
        }

        if (! $node instanceof Closure && ! $node instanceof ArrowFunction && ! $node instanceof Function_ && ! $node instanceof ClassMethod && ! $node instanceof Arg && ! $node instanceof Foreach_ && ! $node instanceof ArrayItem) {
            return [];
        }

        if ($node->byRef) {
            $errorMessages[] = $this->createRuleError();
        }

        if ($node instanceof Function_ || $node instanceof ClassMethod) {
            $paramErrorMessage = $this->collectParamErrorMessages($node, $scope);
            $errorMessages = array_merge($errorMessages, $paramErrorMessage);
        }

        return $errorMessages;
    }

    /**
     * @return list<IdentifierRuleError>
     * @param \PhpParser\Node\Stmt\Function_|\PhpParser\Node\Stmt\ClassMethod $functionLike
     */
    private function collectParamErrorMessages($functionLike, Scope $scope): array
    {
        // has parent method? → skip it as enforced by parent
        $methodName = (string) $functionLike->name;

        $parentClassMethod = $this->parentClassMethodNodeResolver->resolveParentClassMethod($scope, $methodName);
        if ($parentClassMethod instanceof ClassMethod) {
            return [];
        }

        $errorMessages = [];
        foreach ($functionLike->params as $param) {
            /** @var Param $param */
            if (! $param->byRef) {
                continue;
            }

            $errorMessages[] = $this->createRuleError();
        }

        return $errorMessages;
    }

    private function createRuleError(): IdentifierRuleError
    {
        return RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::NO_REFERENCE)
            ->build();
    }
}
