<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Domain;

use Entropy\Utils\Strings;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<InClassNode>
 * @see \Symplify\PHPStanRules\Tests\Rules\Domain\RequireExceptionNamespaceRule\RequireExceptionNamespaceRuleTest
 */
final class RequireExceptionNamespaceRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Exception must be located in "Exception" namespace';

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

        if (! $classReflection->isClass()) {
            return [];
        }

        if (! $classReflection->is('Exception')) {
            return [];
        }

        // is class in "Exception" namespace?
        $className = $classReflection->getName();
        if (Strings::contains($className, '\\Exception\\')) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier('symplify.requireExceptionNamespace')
            ->build()];
    }
}
