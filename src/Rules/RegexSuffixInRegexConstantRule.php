<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\NodeAnalyzer\RegexFuncCallAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\RegexStaticCallAnalyzer;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @implements Rule<CallLike>
 * @see \Symplify\PHPStanRules\Tests\Rules\RegexSuffixInRegexConstantRule\RegexSuffixInRegexConstantRuleTest
 */
final class RegexSuffixInRegexConstantRule implements Rule
{
    /**
     * @readonly
     */
    private RegexFuncCallAnalyzer $regexFuncCallAnalyzer;
    /**
     * @readonly
     */
    private RegexStaticCallAnalyzer $regexStaticCallAnalyzer;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Name your constant with "_REGEX" suffix, instead of "%s"';

    public function __construct(RegexFuncCallAnalyzer $regexFuncCallAnalyzer, RegexStaticCallAnalyzer $regexStaticCallAnalyzer)
    {
        $this->regexFuncCallAnalyzer = $regexFuncCallAnalyzer;
        $this->regexStaticCallAnalyzer = $regexStaticCallAnalyzer;
    }

    public function getNodeType(): string
    {
        return CallLike::class;
    }

    /**
     * @param CallLike $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node instanceof FuncCall) {
            return $this->processFuncCall($node);
        }

        if ($node instanceof StaticCall) {
            return $this->processStaticCall($node);
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public const SOME_NAME = '#some\s+name#';

    public function run($value)
    {
        $somePath = preg_match(self::SOME_NAME, $value);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public const SOME_NAME_REGEX = '#some\s+name#';

    public function run($value)
    {
        $somePath = preg_match(self::SOME_NAME_REGEX, $value);
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return list<RuleError>
     */
    private function processConstantName(Expr $expr): array
    {
        if (! $expr instanceof ClassConstFetch) {
            return [];
        }

        if ($expr->name instanceof Expr) {
            return [];
        }

        $constantName = (string) $expr->name;
        if (substr_compare($constantName, '_REGEX', -strlen('_REGEX')) === 0) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $constantName);
        return [RuleErrorBuilder::message($errorMessage)->build()];
    }

    /**
     * @return list<RuleError>
     */
    private function processStaticCall(StaticCall $staticCall): array
    {
        if (! $this->regexStaticCallAnalyzer->isRegexStaticCall($staticCall)) {
            return [];
        }

        $secondArg = $staticCall->getArgs()[1];
        return $this->processConstantName($secondArg->value);
    }

    /**
     * @return list<RuleError>
     */
    private function processFuncCall(FuncCall $funcCall): array
    {
        if (! $this->regexFuncCallAnalyzer->isRegexFuncCall($funcCall)) {
            return [];
        }

        $firstArg = $funcCall->getArgs()[0];
        return $this->processConstantName($firstArg->value);
    }
}
