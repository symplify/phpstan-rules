<?php

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\NodeAnalyzer\SymfonyRequiredMethodAnalyzer;

/**
 * @implements Rule<Class_>
 */
final class SingleRequiredMethodRule implements Rule
{
    public const ERROR_MESSAGE = 'Found %d @required methods. Use only one method to avoid unexpected behavior.';

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
            if (! SymfonyRequiredMethodAnalyzer::detect($classMethod)) {
                continue;
            }

            ++$requiredClassMethodCount;
        }

        if ($requiredClassMethodCount < 2) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $requiredClassMethodCount);

        $identifierRuleError = RuleErrorBuilder::message($errorMessage)
            ->identifier(SymfonyRuleIdentifier::SINGLE_REQUIRED_METHOD)
            ->build();

        return [$identifierRuleError];
    }
}
