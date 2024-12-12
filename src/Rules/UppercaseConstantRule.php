<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @implements Rule<ClassConst>
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
            return [RuleErrorBuilder::message($errorMessage)->build()];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public const some = 'value';
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public const SOME = 'value';
}
CODE_SAMPLE
            ),
        ]);
    }
}
