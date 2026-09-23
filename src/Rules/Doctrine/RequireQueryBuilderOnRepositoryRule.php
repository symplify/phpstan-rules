<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use Symplify\PHPStanRules\Enum\DoctrineClass;
use Symplify\PHPStanRules\Enum\RuleIdentifier\DoctrineRuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;

/**
 * @implements Rule<InClassMethodNode>
 * @see \Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\RequireQueryBuilderOnRepositoryRuleTest
 */
final readonly class RequireQueryBuilderOnRepositoryRule implements Rule
{
    public const string ERROR_MESSAGE = 'Avoid calling ->createQueryBuilder() directly on EntityManager inside a repository class, as it requires select() + from() calls with specific values. Use $repository->createQueryBuilder() to be safe instead';

    /**
     * UPDATE/DELETE builders are not plain SELECTs, the repository shortcut cannot express them
     * @var string[]
     */
    private const array NON_SELECT_BUILDER_METHODS = ['update', 'delete'];

    private NodeFinder $nodeFinder;

    public function __construct()
    {
        $this->nodeFinder = new NodeFinder();
    }

    public function getNodeType(): string
    {
        return InClassMethodNode::class;
    }

    /**
     * @param InClassMethodNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classMethod = $node->getOriginalNode();
        if ($classMethod->stmts === null) {
            return [];
        }

        // only relevant inside repository classes, where $this->createQueryBuilder() is available as a safe swap
        if (! $this->isInsideRepositoryClass($scope)) {
            return [];
        }

        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->nodeFinder->findInstanceOf($classMethod->stmts, MethodCall::class);

        $repositoryEntityClassNames = $this->resolveRepositoryEntityClassNames($scope);

        $ruleErrors = [];
        foreach ($methodCalls as $methodCall) {
            if (! NamingHelper::isName($methodCall->name, 'createQueryBuilder')) {
                continue;
            }

            $callerType = $scope->getType($methodCall->var);
            if ($this->isValidRepositoryObjectType($callerType)) {
                continue;
            }

            // the query builder cannot be swapped for $this->createQueryBuilder()
            if ($this->isUnconvertibleBuilder($methodCall, $methodCalls, $classMethod, $repositoryEntityClassNames, $scope)) {
                continue;
            }

            $ruleErrors[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->identifier(DoctrineRuleIdentifier::REQUIRE_QUERY_BUILDER_ON_REPOSITORY)
                ->line($methodCall->getStartLine())
                ->build();
        }

        return $ruleErrors;
    }

    private function isInsideRepositoryClass(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        if ($classReflection->isSubclassOf(DoctrineClass::ENTITY_REPOSITORY)) {
            return true;
        }

        return $classReflection->isSubclassOf(DoctrineClass::DOCUMENT_REPOSITORY);
    }

    private function isValidRepositoryObjectType(Type $type): bool
    {
        if ($type instanceof UnionType) {
            foreach ($type->getTypes() as $unionType) {
                if ($this->isValidRepositoryObjectType($unionType)) {
                    return true;
                }
            }
        }

        if (! $type instanceof ObjectType) {
            return true;
        }

        // we safe as both select() + from() calls are made on the repository
        if ($type->isInstanceOf(DoctrineClass::ENTITY_REPOSITORY)->yes()) {
            return true;
        }

        if ($type->isInstanceOf(DoctrineClass::DOCUMENT_REPOSITORY)->yes()) {
            return true;
        }

        return $type->isInstanceOf(DoctrineClass::CONNECTION)->yes();
    }

    /**
     * @param MethodCall[] $allMethodCalls
     * @param string[] $repositoryEntityClassNames
     */
    private function isUnconvertibleBuilder(
        MethodCall $methodCall,
        array $allMethodCalls,
        ClassMethod $classMethod,
        array $repositoryEntityClassNames,
        Scope $scope
    ): bool {
        $builderMethodCalls = $this->collectBuilderMethodCalls($methodCall, $allMethodCalls, $classMethod);

        foreach ($builderMethodCalls as $builderMethodCall) {
            if (! $builderMethodCall->name instanceof Identifier) {
                continue;
            }

            $calledMethodName = $builderMethodCall->name->toString();

            if (in_array($calledMethodName, self::NON_SELECT_BUILDER_METHODS, true)) {
                return true;
            }

            // from() on another entity than the repository's own one cannot use the repository shortcut
            if ($calledMethodName === 'from' && ! $this->isFromOnRepositoryEntity(
                $builderMethodCall,
                $repositoryEntityClassNames,
                $scope
            )) {
                return true;
            }
        }

        return false;
    }

    /**
     * Collects every method call made on the query builder that this createQueryBuilder() call produces,
     * both as a fluent chain and via an intermediate variable.
     *
     * @param MethodCall[] $allMethodCalls
     * @return MethodCall[]
     */
    private function collectBuilderMethodCalls(
        MethodCall $createQueryBuilderCall,
        array $allMethodCalls,
        ClassMethod $classMethod
    ): array {
        $builderMethodCalls = [];

        foreach ($allMethodCalls as $methodCall) {
            if ($methodCall !== $createQueryBuilderCall && $this->chainPassesThrough($methodCall, $createQueryBuilderCall)) {
                $builderMethodCalls[] = $methodCall;
            }
        }

        $assignedVariableName = $this->resolveAssignedVariableName($createQueryBuilderCall, $classMethod);
        if ($assignedVariableName !== null) {
            foreach ($allMethodCalls as $allMethodCall) {
                if ($this->resolveRootVariableName($allMethodCall) === $assignedVariableName) {
                    $builderMethodCalls[] = $allMethodCall;
                }
            }
        }

        return $builderMethodCalls;
    }

    private function chainPassesThrough(MethodCall $methodCall, MethodCall $targetMethodCall): bool
    {
        $current = $methodCall->var;
        while ($current instanceof MethodCall) {
            if ($current === $targetMethodCall) {
                return true;
            }

            $current = $current->var;
        }

        return false;
    }

    private function resolveRootVariableName(MethodCall $methodCall): ?string
    {
        $current = $methodCall->var;
        while ($current instanceof MethodCall) {
            $current = $current->var;
        }

        if ($current instanceof Variable && is_string($current->name)) {
            return $current->name;
        }

        return null;
    }

    private function resolveAssignedVariableName(MethodCall $methodCall, ClassMethod $classMethod): ?string
    {
        /** @var Assign[] $assigns */
        $assigns = $this->nodeFinder->findInstanceOf((array) $classMethod->stmts, Assign::class);

        foreach ($assigns as $assign) {
            $isBuilderAssign = $assign->expr === $methodCall
                || ($assign->expr instanceof MethodCall && $this->chainPassesThrough($assign->expr, $methodCall));

            if (! $isBuilderAssign) {
                continue;
            }

            if ($assign->var instanceof Variable && is_string($assign->var->name)) {
                return $assign->var->name;
            }
        }

        return null;
    }

    /**
     * @param string[] $repositoryEntityClassNames
     */
    private function isFromOnRepositoryEntity(MethodCall $fromMethodCall, array $repositoryEntityClassNames, Scope $scope): bool
    {
        $args = $fromMethodCall->getArgs();
        if (! isset($args[0])) {
            return false;
        }

        $firstArg = $args[0]->value;
        if (! $firstArg instanceof ClassConstFetch) {
            return false;
        }

        if (! $firstArg->class instanceof Name) {
            return false;
        }

        if (! $firstArg->name instanceof Identifier || $firstArg->name->toString() !== 'class') {
            return false;
        }

        $fromClassName = $scope->resolveName($firstArg->class);

        return in_array($fromClassName, $repositoryEntityClassNames, true);
    }

    /**
     * @return string[]
     */
    private function resolveRepositoryEntityClassNames(Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        $entityClassNames = [];
        foreach ([DoctrineClass::ENTITY_REPOSITORY, DoctrineClass::DOCUMENT_REPOSITORY] as $repositoryBaseClass) {
            $ancestorClassReflection = $classReflection->getAncestorWithClassName($repositoryBaseClass);
            if (! $ancestorClassReflection instanceof ClassReflection) {
                continue;
            }

            foreach ($ancestorClassReflection->getActiveTemplateTypeMap()->getTypes() as $templateType) {
                foreach ($templateType->getObjectClassNames() as $objectClassName) {
                    $entityClassNames[] = $objectClassName;
                }
            }
        }

        return array_unique($entityClassNames);
    }
}
