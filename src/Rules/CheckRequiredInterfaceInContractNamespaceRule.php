<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Entropy\Utils\Regex;
use PhpParser\Node;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckRequiredInterfaceInContractNamespaceRule\CheckRequiredInterfaceInContractNamespaceRuleTest
 * @implements Rule<Interface_>
 */
final class CheckRequiredInterfaceInContractNamespaceRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Interface must be located in "Contract", "Contracts", "Interface" or "Interfaces" namespace';

    /**
     * @see https://regex101.com/r/kmrIG1/2
     * @var string
     */
    private const A_CONTRACT_NAMESPACE_REGEX = '#\b(Contracts?|Interfaces?)\b#';

    public function getNodeType(): string
    {
        return Interface_::class;
    }

    /**
     * @param Interface_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();
        if ($namespace === null) {
            return [];
        }

        if (Regex::match($namespace, self::A_CONTRACT_NAMESPACE_REGEX) !== []) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier('symplify.requiredInterfaceContractNamespace')
            ->build()];
    }
}
