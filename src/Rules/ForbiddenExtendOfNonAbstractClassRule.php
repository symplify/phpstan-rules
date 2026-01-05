<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<InClassNode>
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenExtendOfNonAbstractClassRule\ForbiddenExtendOfNonAbstractClassRuleTest
 */
final class ForbiddenExtendOfNonAbstractClassRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Only abstract classes can be extended';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();

        if ($classReflection->isAnonymous()) {
            return [];
        }

        $parentClassReflection = $classReflection->getParentClass();
        if (! $parentClassReflection instanceof ClassReflection) {
            return [];
        }

        if ($parentClassReflection->isAbstract()) {
            return [];
        }

        // skip native PHP classes
        if ($parentClassReflection->isBuiltin()) {
            return [];
        }

        // skip vendor based classes, as designed for extension
        $fileName = $parentClassReflection->getFileName();
        if (is_string($fileName) && strpos($fileName, 'vendor') !== false) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::FORBIDDEN_EXTEND_OF_NON_ABSTRACT_CLASS)
            ->build()];
    }
}
