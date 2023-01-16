<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Missing;

use Nette\Utils\Arrays;
use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\Constant\ConstantStringType;
use Symplify\PHPStanRules\NodeAnalyzer\SprintfSpecifierTypeResolver;
use Symplify\PHPStanRules\TypeAnalyzer\MatchingTypeAnalyzer;
use Symplify\PHPStanRules\TypeResolver\ArgTypeResolver;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Missing\CheckSprintfMatchingTypesRule\CheckSprintfMatchingTypesRuleTest
 *
 * @inspiration by https://github.com/phpstan/phpstan-src/blob/master/src/Rules/Functions/PrintfParametersRule.php
 */
final class CheckSprintfMatchingTypesRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'sprintf() call mask types does not match provided arguments types';

    /**
     * @var string
     */
    private const SPECIFIERS = '[bcdeEfFgGosuxX%s]';

    public function __construct(
        private SprintfSpecifierTypeResolver $sprintfSpecifierTypeResolver,
        private MatchingTypeAnalyzer $matchingTypeAnalyzer,
        private ArgTypeResolver $argTypeResolver,
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * @param  FuncCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Name) {
            return [];
        }

        $funcCallName = $node->name->toString();
        if ($funcCallName !== 'sprintf') {
            return [];
        }

        $firstArg = $node->getArgs()[0];
        $formatArgType = $scope->getType($firstArg->value);

        if (! $formatArgType instanceof ConstantStringType) {
            return [];
        }

        $specifiersMatches = $this->resolveSpecifierMatches($formatArgType);

        $argTypes = $this->argTypeResolver->resolveArgTypesWithoutFirst($node, $scope);
        $expectedTypesByPosition = $this->sprintfSpecifierTypeResolver->resolveFromSpecifiers($specifiersMatches);

        // miss-matching count, handled by native PHPStan rule
        if (count($argTypes) !== count($expectedTypesByPosition)) {
            return [];
        }

        foreach ($argTypes as $key => $argType) {
            $expectedTypes = $expectedTypesByPosition[$key];

            if ($this->matchingTypeAnalyzer->isTypeMatchingExpectedTypes($argType, $expectedTypes)) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            self::ERROR_MESSAGE,
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
echo sprintf('My name is %s and I have %d children', 10, 'Tomas');

CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
echo sprintf('My name is %s and I have %d children', 'Tomas', 10);
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @see    https://github.com/phpstan/phpstan-src/blob/e10a7aac373e8b6f21b430034fc693300c2bbb69/src/Rules/Functions/PrintfParametersRule.php#L105-L115
     * @return string[]
     */
    private function resolveSpecifierMatches(ConstantStringType $constantStringType): array
    {
        $value = $constantStringType->getValue();
        $pattern = '#%(?:(?<position>\d+)\$)?[-+]?(?:[ 0]|(?:\'[^%]))?-?\d*(?:\.\d*)?' . self::SPECIFIERS . '#';

        $allMatches = Strings::matchAll($value, $pattern);
        return Arrays::flatten($allMatches);
    }
}
