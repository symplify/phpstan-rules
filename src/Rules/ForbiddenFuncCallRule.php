<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use SimpleXMLElement;
use Symplify\PHPStanRules\Formatter\RequiredWithMessageFormatter;
use Symplify\PHPStanRules\Matcher\ArrayStringAndFnMatcher;
use Symplify\PHPStanRules\ValueObject\Configuration\RequiredWithMessage;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenFuncCallRule\ForbiddenFuncCallRuleTest
 */
final class ForbiddenFuncCallRule implements Rule, DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Function "%s()" cannot be used/left in the code';

    /**
     * @param string[]|array<string|int, string> $forbiddenFunctions
     */
    public function __construct(
        private readonly array $forbiddenFunctions,
        private readonly ArrayStringAndFnMatcher $arrayStringAndFnMatcher,
        private readonly RequiredWithMessageFormatter $requiredWithMessageFormatter,
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
     * @param FuncCall $node
     * @return string[]
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
            return [$errorMessage];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
echo eval('...');
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
echo '...';
CODE_SAMPLE
                ,
                [
                    'forbiddenFunctions' => ['eval'],
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
dump($value);
echo $value;
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
echo $value;
CODE_SAMPLE
                ,
                [
                    'forbiddenFunctions' => [
                        'dump' => 'seems you missed some debugging function',
                    ],
                ]
            ),
        ]);
    }

    private function shouldAllowSpecialCase(FuncCall $funcCall, Scope $scope, string $functionName): bool
    {
        if ($functionName !== 'property_exists') {
            return false;
        }

        $argOrVariadicPlaceholder = $funcCall->args[0];
        if (! $argOrVariadicPlaceholder instanceof Arg) {
            return false;
        }

        $firstArgValue = $argOrVariadicPlaceholder->value;

        $firstArgType = $scope->getType($firstArgValue);
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
