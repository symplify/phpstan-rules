<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Name\Relative;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Rector\NoLeadingBackslashInNameRule\NoLeadingBackslashInNameRuleTest
 *
 * @implements Rule<New_>
 */
final class NoLeadingBackslashInNameRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of "new Name(\'\\\\Foo\')" use "new FullyQualified(\'Foo\')"';

    public function getNodeType(): string
    {
        return New_::class;
    }

    /**
     * @param New_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->getArgs() === []) {
            return [];
        }

        if (! $node->class instanceof Name) {
            return [];
        }

        $className = $node->class->toString();
        if (! in_array($className, [Name::class, FullyQualified::class, Relative::class], true)) {
            return [];
        }

        $argValue = $node->getArgs()[0]->value;
        $argType = $scope->getType($argValue);

        if (! $argType instanceof ConstantStringType) {
            return [];
        }

        if (strncmp($argType->getValue(), '\\', strlen('\\')) !== 0) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::PHP_PARSER_NO_LEADING_BACKSLASH_IN_NAME)
            ->build()];
    }
}
