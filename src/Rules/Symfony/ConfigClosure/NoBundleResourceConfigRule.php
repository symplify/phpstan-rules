<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony\ConfigClosure;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyClosureDetector;

/**
 * @implements Rule<Closure>
 */
final class NoBundleResourceConfigRule implements Rule
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Avoid using configs in Bundle/Resources directory. Move them to "/config" directory instead';

    public function getNodeType(): string
    {
        return Closure::class;
    }

    /**
     * @param Closure $node
     * @return IdentifierRuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! SymfonyClosureDetector::detect($node)) {
            return [];
        }

        if (strpos($scope->getFile(), 'Resources/config') === false) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(SymfonyRuleIdentifier::NO_BUNDLE_RESOURCE_CONFIG)
            ->build();

        return [$identifierRuleError];
    }
}
