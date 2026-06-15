<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use SimpleXMLElement;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Symplify\PHPStanRules\Formatter\RequiredWithMessageFormatter;
use Symplify\PHPStanRules\Matcher\ArrayStringAndFnMatcher;
use Symplify\PHPStanRules\ValueObject\Configuration\RequiredWithMessage;

/**
 * @implements Rule<FuncCall>
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenFuncCallRule\ForbiddenFuncCallRuleTest
 */
final class ForbiddenFuncCallRule implements Rule
{
    /**
     * @var array<string>
     * @readonly
     */
    private array $forbiddenFunctions;
    /**
     * @readonly
     */
    private ArrayStringAndFnMatcher $arrayStringAndFnMatcher;
    /**
     * @readonly
     */
    private RequiredWithMessageFormatter $requiredWithMessageFormatter;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Function "%s()" cannot be used/left in the code';

    /**
     * @param array<string> $forbiddenFunctions
     */
    public function __construct(array $forbiddenFunctions, ArrayStringAndFnMatcher $arrayStringAndFnMatcher, RequiredWithMessageFormatter $requiredWithMessageFormatter)
    {
        $this->forbiddenFunctions = $forbiddenFunctions;
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
        $this->requiredWithMessageFormatter = $requiredWithMessageFormatter;
    }

    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * @param FuncCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Name) {
            return [];
        }

        $funcName = $node->name->toString();

        $requiredWithMessages = $this->requiredWithMessageFormatter->normalizeConfig($this->forbiddenFunctions);
        foreach ($requiredWithMessages as $requiredWithMessage) {
            if (! $this->arrayStringAndFnMatcher->isMatch($funcName, [$requiredWithMessage->getRequired()])) {
                continue;
            }

            // special cases
            if ($this->shouldAllowSpecialCase($node, $scope, $funcName)) {
                continue;
            }

            $errorMessage = $this->createErrorMessage($requiredWithMessage, $funcName);

            $ruleError = RuleErrorBuilder::message($errorMessage)
                ->identifier(RuleIdentifier::FORBIDDEN_FUNC_CALL)
                ->build();

            return [$ruleError];
        }

        return [];
    }

    private function shouldAllowSpecialCase(FuncCall $funcCall, Scope $scope, string $functionName): bool
    {
        if ($functionName !== 'property_exists') {
            return false;
        }

        $arg = $funcCall->getArgs()[0];

        $firstArgType = $scope->getType($arg->value);

        // non nullable
        $firstArgType = TypeCombinator::removeNull($firstArgType);

        $simpleXmlElementObjectType = new ObjectType(SimpleXMLElement::class);
        return $simpleXmlElementObjectType->isSuperTypeOf($firstArgType)
            ->yes();
    }

    private function createErrorMessage(RequiredWithMessage $requiredWithMessage, string $funcName): string
    {
        if ($requiredWithMessage->getMessage() === null) {
            return sprintf(self::ERROR_MESSAGE, $funcName);
        }

        return sprintf(self::ERROR_MESSAGE . ': ' . $requiredWithMessage->getMessage(), $funcName);
    }
}
