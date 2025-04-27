<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\DoctrineClass;
use Symplify\PHPStanRules\Enum\RuleIdentifier\DoctrineRuleIdentifier;

/**
 * @implements Rule<InClassNode>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireServiceRepositoryParentRuleTest\RequireServiceRepositoryParentRuleTest
 */
final class RequireServiceRepositoryParentRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Repository must extend "%s", "%s" or implement "%s", so it can be injected as a service';

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();

        // no parent? probably not a repository service yet
        if (! $this->isDoctrineRepositoryClass($classReflection)) {
            return [];
        }

        if ($this->isExtendingServiceRepository($classReflection)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, DoctrineClass::ODM_SERVICE_REPOSITORY, DoctrineClass::ORM_SERVICE_REPOSITORY, DoctrineClass::ODM_SERVICE_REPOSITORY_INTERFACE);

        $identifierRuleError = RuleErrorBuilder::message($errorMessage)
            ->identifier(DoctrineRuleIdentifier::REQUIRE_SERVICE_PARENT_REPOSITORY)
            ->build();

        return [$identifierRuleError];
    }

    private function isExtendingServiceRepository(ClassReflection $classReflection): bool
    {
        if ($classReflection->is(DoctrineClass::ODM_SERVICE_REPOSITORY)) {
            return true;
        }

        if ($classReflection->is(DoctrineClass::ORM_SERVICE_REPOSITORY)) {
            return true;
        }

        return $classReflection->is(DoctrineClass::ODM_SERVICE_REPOSITORY_INTERFACE);
    }

    private function isDoctrineRepositoryClass(ClassReflection $classReflection): bool
    {
        if (! $classReflection->isClass()) {
            return false;
        }

        // simple check
        return substr_compare($classReflection->getName(), 'Repository', -strlen('Repository')) === 0;
    }
}
