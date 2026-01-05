<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<Class_>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\NoMissnamedDocTagRule\NoMissnamedDocTagRuleTest
 */
final class NoMissnamedDocTagRule implements Rule
{
    /**
     * @api used in tests
     * @var string
     */
    public const CONSTANT_ERROR_MESSAGE = 'Constant doc comment tag must be @var, "%s" given';

    /**
     * @api used in tests
     * @var string
     */
    public const PROPERTY_ERROR_MESSAGE = 'Property doc comment tag must be @var, "%s" given';

    /**
     * @api used in tests
     * @var string
     */
    public const METHOD_ERROR_MESSAGE = 'Method doc comment tag must be @param or @return, "%s" given';

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return array<RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $ruleErrors = [];

        foreach ($node->getMethods() as $classMethod) {
            // match "@return" and "@param" tags
            if ($classMethod->getDocComment() === null) {
                continue;
            }

            $matches = Strings::match($classMethod->getDocComment()->getText(), '#\*\s(@var)\b#mi');
            if ($matches === null) {
                continue;
            }

            $ruleErrors[] = RuleErrorBuilder::message(sprintf(self::METHOD_ERROR_MESSAGE, $matches[1]))
                ->identifier(RuleIdentifier::NO_MISSNAMED_DOC_TAG)
                ->line($classMethod->getStartLine())
                ->build();
        }

        foreach ($node->getProperties() as $property) {
            // match "@return" and "@param" tags
            if ($property->getDocComment() === null) {
                continue;
            }

            $matches = Strings::match($property->getDocComment()->getText(), '#\*\s(@param|@return)\b#mi');
            if ($matches === null) {
                continue;
            }

            $ruleErrors[] = RuleErrorBuilder::message(sprintf(self::PROPERTY_ERROR_MESSAGE, $matches[1]))
                ->identifier(RuleIdentifier::NO_MISSNAMED_DOC_TAG)
                ->line($property->getStartLine())
                ->build();
        }

        foreach ($node->getConstants() as $classConst) {
            if ($classConst->getDocComment() === null) {
                continue;
            }

            $matches = Strings::match($classConst->getDocComment()->getText(), '#(@param|@return)\b#mi');
            if ($matches === null) {
                continue;
            }

            $ruleErrors[] = RuleErrorBuilder::message(sprintf(self::CONSTANT_ERROR_MESSAGE, $matches[1]))
                ->identifier(RuleIdentifier::NO_MISSNAMED_DOC_TAG)
                ->line($classConst->getStartLine())
                ->build();
        }

        return $ruleErrors;
    }
}
