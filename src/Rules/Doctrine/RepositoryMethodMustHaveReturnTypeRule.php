<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Every public method on a "*Repository" class must declare a return type.
 *
 * A repository is a data-access contract; a missing return type hides what a query method hands back and drops
 * static checks at every call site. Magic methods (e.g. "__construct") are skipped, as PHP forbids a return type
 * on them.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Doctrine\RepositoryMethodMustHaveReturnTypeRule\RepositoryMethodMustHaveReturnTypeRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final class RepositoryMethodMustHaveReturnTypeRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Public method "%s()" of a repository class must declare a return type';

    /**
     * @var string
     */
    private const REPOSITORY_SUFFIX = 'Repository';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->isPublic()) {
            return [];
        }

        // magic methods like "__construct" cannot declare a return type
        if (strncmp($node->name->toString(), '__', strlen('__')) === 0) {
            return [];
        }

        if ($node->returnType !== null) {
            return [];
        }

        // inside a trait the scope class is the repository using it, so the method would be checked repeatedly
        if ($scope->isInTrait()) {
            return [];
        }

        if (! $scope->isInClass()) {
            return [];
        }

        $classReflection = $scope->getClassReflection();

        // anonymous classes are local one-off implementations, skip them
        if ($classReflection->isAnonymous()) {
            return [];
        }

        if (substr_compare($classReflection->getName(), self::REPOSITORY_SUFFIX, -strlen(self::REPOSITORY_SUFFIX)) !== 0) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $node->name->toString());

        return [
            RuleErrorBuilder::message($errorMessage)
                ->identifier('doctrine.repositoryMethodMustHaveReturnType')
                ->line($node->getStartLine())
                ->build(),
        ];
    }
}
