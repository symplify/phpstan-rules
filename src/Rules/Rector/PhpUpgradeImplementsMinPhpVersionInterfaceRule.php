<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Rector;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\PHPStanRules\Enum\RuleIdentifier\RectorRuleIdentifier;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Rector\PhpUpgradeImplementsMinPhpVersionInterfaceRule\PhpUpgradeImplementsMinPhpVersionInterfaceRuleTest
 *
 * @implements Rule<Class_>
 */
final class PhpUpgradeImplementsMinPhpVersionInterfaceRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Rule %s must implements Rector\VersionBonding\Contract\MinPhpVersionInterface';

    /**
     * @var string
     * @see https://regex101.com/r/9d3jGP/2/
     */
    private const PREFIX_REGEX = '#\\\\Php\d+\\\\#';

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        /** @var string $className */
        $className = (string) $node->namespacedName;
        if (substr_compare($className, 'Rector', -strlen('Rector')) !== 0) {
            return [];
        }

        if (Strings::match($className, self::PREFIX_REGEX) === null) {
            return [];
        }

        $implements = $node->implements;
        foreach ($implements as $implement) {
            if (! $implement instanceof FullyQualified) {
                continue;
            }

            if ($implement->toString() !== MinPhpVersionInterface::class) {
                continue;
            }

            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $className))
            ->identifier(RectorRuleIdentifier::PHP_RULE_IMPLEMENTS_MIN_VERSION)
            ->build();

        return [$identifierRuleError];
    }
}
