<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Convention;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Webmozart\Assert\Assert;

/**
 * @implements Rule<Param>
 * @see \Symplify\PHPStanRules\Tests\Rules\Convention\ParamNameToTypeConventionRule\ParamNameToTypeConventionRuleTest
 */
final class ParamNameToTypeConventionRule implements Rule
{
    /**
     * @var array<string, string>
     */
    private array $paramNamesToTypes;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Parameter name "$%s" should probably have "%s" type';

    /**
     * @param array<string, string> $paramNamesToTypes
     */
    public function __construct(
        array $paramNamesToTypes
    ) {
        $this->paramNamesToTypes = $paramNamesToTypes;
        Assert::notEmpty($paramNamesToTypes);

        Assert::allString(array_keys($paramNamesToTypes));
        Assert::allString($paramNamesToTypes);
    }

    public function getNodeType(): string
    {
        return Param::class;
    }

    /**
     * @param Param $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // param type is known, let's skip it
        if ($node->type instanceof Node) {
            return [];
        }

        // unable to fill the type
        if ($node->variadic) {
            return [];
        }

        if (! $node->var instanceof Variable) {
            return [];
        }

        if (! is_string($node->var->name)) {
            return [];
        }

        $variableName = $node->var->name;

        $expectedType = $this->paramNamesToTypes[$variableName] ?? null;
        if ($expectedType === null) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $variableName, $expectedType);

        $identifierRuleError = RuleErrorBuilder::message($errorMessage)
            ->identifier(RuleIdentifier::CONVENTION_PARAM_NAME_TO_TYPE)
            ->build();

        return [$identifierRuleError];
    }
}
