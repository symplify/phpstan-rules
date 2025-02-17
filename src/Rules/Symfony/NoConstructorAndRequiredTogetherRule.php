<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\MethodName;
use Symplify\PHPStanRules\Enum\SymfonyRuleIdentifier;

/**
 * @implements Rule<Class_>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\NoConstructorAndRequiredTogetherRule\NoConstructorAndRequiredTogetherRuleTest
 */
final class NoConstructorAndRequiredTogetherRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid using __construct() and @required in the same class. Pick one to keep architecture clean';

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return IdentifierRuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->isAnonymous()) {
            return [];
        }

        if (! $node->getMethod(MethodName::CONSTRUCTOR)) {
            return [];
        }

        if (! $this->hasAutowiredMethod($node)) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(SymfonyRuleIdentifier::NO_CONSTRUCT_AND_REQUIRED)
            ->build();

        return [
            $identifierRuleError,
        ];
    }

    private function hasAutowiredMethod(Class_ $class): bool
    {
        foreach ($class->getMethods() as $classMethod) {
            if (! $classMethod->isPublic()) {
                continue;
            }

            $docComment = $classMethod->getDocComment();
            if (! $docComment instanceof Doc) {
                continue;
            }

            if (strpos($docComment->getText(), '@required') === false) {
                continue;
            }

            // special case when its allowed, to avoid circular references
            if (strpos($docComment->getText(), 'circular') !== false) {
                continue;
            }

            return true;
        }

        return false;
    }
}
