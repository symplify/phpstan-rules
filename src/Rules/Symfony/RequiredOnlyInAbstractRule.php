<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\NodeAnalyzer\SymfonyRequiredMethodAnalyzer;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\RequiredOnlyInAbstractRule\RequiredOnlyInAbstractRuleTest
 *
 * @implements Rule<Class_>
 */
final class RequiredOnlyInAbstractRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '#@required is reserved exclusively for abstract classes. For the rest of classes, use clean constructor injection';

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        foreach ($node->getMethods() as $classMethod) {
            if (! SymfonyRequiredMethodAnalyzer::detect($classMethod)) {
                continue;
            }

            if ($node->isAbstract()) {
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
}
