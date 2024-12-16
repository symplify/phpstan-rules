<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Symplify\PHPStanRules\NodeAnalyzer\RegexFuncCallAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\RegexStaticCallAnalyzer;

/**
 * @implements Rule<CallLike>
 * @see \Symplify\PHPStanRules\Tests\Rules\NoInlineStringRegexRule\NoInlineStringRegexRuleTest
 */
final class NoInlineStringRegexRule implements Rule
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
    public const ERROR_MESSAGE = 'Use local named constant instead of inline string for regex to explain meaning by constant name';

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
            return $this->processRegexFuncCall($node);
        }

        if ($node instanceof StaticCall) {
            return $this->processRegexStaticCall($node);
        }

        return [];
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function processRegexFuncCall(FuncCall $funcCall): array
    {
        if (! $this->regexFuncCallAnalyzer->isRegexFuncCall($funcCall)) {
            return [];
        }

        $firstArg = $funcCall->getArgs()[0];

        // it's not string → good
        if (! $firstArg->value instanceof String_) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::CLASS_CONSTANT_REGEX)
            ->build()];
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function processRegexStaticCall(StaticCall $staticCall): array
    {
        if (! $this->regexStaticCallAnalyzer->isRegexStaticCall($staticCall)) {
            return [];
        }

        $secondArg = $staticCall->getArgs()[1];
        $secondArgValue = $secondArg->value;

        // it's not string → good
        if (! $secondArgValue instanceof String_) {
            return [];
        }

        $regexValue = $secondArgValue->value;

        if (strlen($regexValue) <= 7) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::CLASS_CONSTANT_REGEX)
            ->build()];
    }
}
