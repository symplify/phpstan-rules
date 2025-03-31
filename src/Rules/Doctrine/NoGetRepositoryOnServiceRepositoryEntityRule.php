<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use Symplify\PHPStanRules\Doctrine\RepositoryClassResolver;
use Symplify\PHPStanRules\Enum\DoctrineClass;
use Symplify\PHPStanRules\Enum\DoctrineRuleIdentifier;
use Symplify\PHPStanRules\Enum\TestClassName;

/**
 * @implements Rule<MethodCall>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule\NoGetRepositoryOnServiceRepositoryEntityRuleTest
 */
final class NoGetRepositoryOnServiceRepositoryEntityRule implements Rule
{
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of calling "->getRepository(%s::class)" service locator, inject service repository "%s" via constructor and use it directly';

    /**
     * @readonly
     */
    private RepositoryClassResolver $repositoryClassResolver;

    public function __construct(
        ReflectionProvider $reflectionProvider
    ) {
        $this->reflectionProvider = $reflectionProvider;
        $this->repositoryClassResolver = new RepositoryClassResolver($reflectionProvider);
    }

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier || $node->name->toString() !== 'getRepository') {
            return [];
        }

        $repositoryClassName = $this->resolveRepositoryClassFromGetRepositoryEntity($node, $scope);
        if (! is_string($repositoryClassName)) {
            return [];
        }

        if ($this->shouldSkipTestClass($scope)) {
            return [];
        }

        // is the repository service one?
        if (! $this->isServiceRepositoryClassReflection($repositoryClassName)) {
            return [];
        }

        /** @var string $entityClassName */
        $entityClassName = $this->resolveEntityClass($node, $scope);
        $shortEntityClassName = Strings::after($entityClassName, '\\', -1);

        $errorMessage = sprintf(self::ERROR_MESSAGE, $shortEntityClassName, $repositoryClassName);

        $identifierRuleError = RuleErrorBuilder::message($errorMessage)
            ->identifier(DoctrineRuleIdentifier::INJECT_SERVICE_REPOSITORY)
            ->build();

        return [$identifierRuleError];
    }

    private function shouldSkipTestClass(Scope $scope): bool
    {
        // skip tests and behat Context
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return true;
        }

        if ($classReflection->is(TestClassName::PHPUNIT_TEST_CASE)) {
            return true;
        }

        return $classReflection->is(TestClassName::BEHAT_CONTEXT);
    }

    private function resolveRepositoryClassFromGetRepositoryEntity(MethodCall $methodCall, Scope $scope): ?string
    {
        $entityClassName = $this->resolveEntityClass($methodCall, $scope);
        if ($entityClassName === null) {
            return null;
        }

        return $this->repositoryClassResolver->resolveFromEntityClass($entityClassName);
    }

    private function resolveEntityClass(MethodCall $methodCall, Scope $scope): ?string
    {
        if (count($methodCall->getArgs()) !== 1) {
            return null;
        }

        $firstArgument = $methodCall->getArgs()[0]->value;

        $entityClassType = $scope->getType($firstArgument);
        if (! $entityClassType instanceof ConstantStringType) {
            return null;
        }

        return $entityClassType->getValue();
    }

    private function isServiceRepositoryClassReflection(string $repositoryClassName): bool
    {
        if (! $this->reflectionProvider->hasClass($repositoryClassName)) {
            return false;
        }

        $repositoryClassReflection = $this->reflectionProvider->getClass($repositoryClassName);
        if ($repositoryClassReflection->is(DoctrineClass::ODM_SERVICE_REPOSITORY)) {
            return true;
        }

        if ($repositoryClassReflection->is(DoctrineClass::ODM_SERVICE_REPOSITORY_INTERFACE)) {
            return true;
        }

        return $repositoryClassReflection->is(DoctrineClass::ORM_SERVICE_REPOSITORY);
    }
}
