<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<ClassConst>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\UppercaseConstantRule\UppercaseConstantRuleTest
 */
final class UppercaseConstantRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Constant "%s" must be uppercase';

    public function getNodeType(): string
    {
        return ClassConst::class;
    }

    /**
     * @param ClassConst $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        foreach ($node->consts as $const) {
            $constantName = (string) $const->name;
            if (strtoupper($constantName) === $constantName) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $constantName);
            return [RuleErrorBuilder::message($errorMessage)
                ->identifier(RuleIdentifier::UPPERCASE_CONSTANT)
                ->build()];
        }

        return [];
    }
}
