<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\NodeAnalyzer\SymfonyRequiredMethodAnalyzer;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\RequiredOnlyInAbstractRule\RequiredOnlyInAbstractRuleTest
 *
 * @implements Rule<InClassNode>
 */
final class RequiredOnlyInAbstractRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '#Symfony @required or #[Required] is reserved exclusively for abstract classes. For the rest of classes, use clean constructor injection';

    /**
     * Magic parent types that require constructor internally,
     * so @required on final class is allowed
     *
     * @var string[]
     */
    private const SKIPPED_PARENT_TYPES = [
        'Doctrine\ODM\MongoDB\Repository\DocumentRepository',
    ];

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        if ($this->shouldSkipClass($scope)) {
            return [];
        }

        $class = $classLike;
        foreach ($class->getMethods() as $classMethod) {
            if (! SymfonyRequiredMethodAnalyzer::detect($classMethod)) {
                continue;
            }

            if ($this->hasCircularDocNote($classMethod)) {
                continue;
            }

            if ($class->isAbstract()) {
                continue;
            }

            $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->line($classMethod->getLine())
                ->identifier(SymfonyRuleIdentifier::SYMFONY_REQUIRED_ONLY_IN_ABSTRACT)
                ->build();

            return [$identifierRuleError];
        }

        return [];
    }

    private function shouldSkipClass(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        if ($classReflection->isAbstract()) {
            return true;
        }

        foreach (self::SKIPPED_PARENT_TYPES as $skippedParentType) {
            if ($classReflection->isSubclassOf($skippedParentType)) {
                return true;
            }
        }

        return false;
    }

    private function hasCircularDocNote(Node $node): bool
    {
        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        return strpos($docComment->getText(), 'circular') !== false;
    }
}
