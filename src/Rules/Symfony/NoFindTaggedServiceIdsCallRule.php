<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;
use Symplify\PHPStanRules\PHPUnit\TestClassDetector;

/**
 * @implements Rule<InClassNode>
 */
final readonly class NoFindTaggedServiceIdsCallRule implements Rule
{
    public const string ERROR_MESSAGE = 'Instead of "$this->findTaggedServiceIds()" use more reliable registerForAutoconfiguration() and tagged iterator attribute. Those work outside any configuration and avoid missed tag errors';

    private NodeFinder $nodeFinder;

    public function __construct()
    {
        $this->nodeFinder = new NodeFinder();
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return IdentifierRuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // tagged service ids are commonly used in tests to assert service registration
        if (TestClassDetector::isTestClass($scope)) {
            return [];
        }

        $classLike = $node->getOriginalNode();

        $ruleErrors = [];

        foreach ($this->findTaggedServiceIdsCalls($classLike) as $methodCall) {
            // reading tag attributes (e.g. $tags[0]['alias']) cannot be expressed by a tagged iterator, keep it
            if ($this->usesTagAttributes($classLike, $methodCall)) {
                continue;
            }

            $ruleErrors[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->identifier(SymfonyRuleIdentifier::NO_FIND_TAGGED_SERVICE_IDS_CALL)
                ->line($methodCall->getStartLine())
                ->build();
        }

        return $ruleErrors;
    }

    /**
     * @return MethodCall[]
     */
    private function findTaggedServiceIdsCalls(Node $classLike): array
    {
        $methodCalls = $this->nodeFinder->findInstanceOf($classLike, MethodCall::class);

        return array_filter(
            $methodCalls,
            static fn (MethodCall $methodCall): bool => NamingHelper::isName($methodCall->name, 'findTaggedServiceIds')
        );
    }

    private function usesTagAttributes(Node $classLike, MethodCall $methodCall): bool
    {
        $assignedVariableName = $this->resolveAssignedVariableName($classLike, $methodCall);
        if ($assignedVariableName === null) {
            return false;
        }

        foreach ($this->findForeachesOverVariable($classLike, $assignedVariableName) as $foreach) {
            if (! $foreach->valueVar instanceof Variable || ! is_string($foreach->valueVar->name)) {
                continue;
            }

            if ($this->hasArrayDimFetchOnVariable($foreach->stmts, $foreach->valueVar->name)) {
                return true;
            }
        }

        return false;
    }

    private function resolveAssignedVariableName(Node $classLike, MethodCall $methodCall): ?string
    {
        foreach ($this->nodeFinder->findInstanceOf($classLike, Assign::class) as $assign) {
            if ($assign->expr !== $methodCall) {
                continue;
            }

            if ($assign->var instanceof Variable && is_string($assign->var->name)) {
                return $assign->var->name;
            }
        }

        return null;
    }

    /**
     * @return Foreach_[]
     */
    private function findForeachesOverVariable(Node $classLike, string $variableName): array
    {
        $foreaches = $this->nodeFinder->findInstanceOf($classLike, Foreach_::class);

        return array_filter(
            $foreaches,
            static fn (Foreach_ $foreach): bool => $foreach->expr instanceof Variable && $foreach->expr->name === $variableName
        );
    }

    /**
     * @param Node[] $stmts
     */
    private function hasArrayDimFetchOnVariable(array $stmts, string $variableName): bool
    {
        $arrayDimFetch = $this->nodeFinder->findFirst($stmts, static function (Node $subNode) use ($variableName): bool {
            if (! $subNode instanceof ArrayDimFetch) {
                return false;
            }

            $rootVariable = $subNode->var;
            while ($rootVariable instanceof ArrayDimFetch) {
                $rootVariable = $rootVariable->var;
            }

            return $rootVariable instanceof Variable && $rootVariable->name === $variableName;
        });

        return $arrayDimFetch instanceof ArrayDimFetch;
    }
}
