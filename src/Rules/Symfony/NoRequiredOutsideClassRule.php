<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\NodeAnalyzer\SymfonyRequiredMethodAnalyzer;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\NoRequiredOutsideClassRule\NoRequiredOutsideClassRuleTest
 *
 * @implements Rule<Trait_>
 */
final class NoRequiredOutsideClassRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Symfony #[Require]/@required should be used only in classes to avoid misuse';

    public function getNodeType(): string
    {
        return Trait_::class;
    }

    /**
     * @param Trait_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $ruleErrors = [];

        foreach ($node->getMethods() as $classMethod) {
            if (! SymfonyRequiredMethodAnalyzer::detect($classMethod)) {
                continue;
            }

            $ruleErrors[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->identifier(SymfonyRuleIdentifier::SYMFONY_NO_REQUIRED_OUTSIDE_CLASS)
                ->line($classMethod->getLine())
                ->build();
        }

        return $ruleErrors;
    }
}
