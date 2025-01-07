<?php

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\SymfonyRuleIdentifier;

/**
 * @implements Rule<Class_>
 */
final class SingleRequiredMethodRule implements Rule
{
    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $requiredClassMethodCount = 0;

        foreach ($node->getMethods() as $classMethod) {
            if (! $classMethod->getDocComment() instanceof Doc) {
                continue;
            }

            $doc = $classMethod->getDocComment();

            if (! str_contains($doc->getText(), '@required')) {
                continue;
            }

            ++$requiredClassMethodCount;
        }

        if ($requiredClassMethodCount === 0) {
            return [];
        }

        if ($requiredClassMethodCount < 2) {
            return [];
        }

        $errorMessage = sprintf(
            'Found %d @required methods. Use only one method to avoid unexpected behavior.',
            $requiredClassMethodCount
        );

        $ruleError = RuleErrorBuilder::message($errorMessage)
            ->identifier(SymfonyRuleIdentifier::SINGLE_REQUIRED_METHOD)
            ->build();

        return [$ruleError];
    }
}
