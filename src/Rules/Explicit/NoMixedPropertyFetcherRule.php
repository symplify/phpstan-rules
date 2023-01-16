<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\MixedType;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedPropertyFetcherRule\NoMixedPropertyFetcherRuleTest
 */
final class NoMixedPropertyFetcherRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Anonymous variables in a "%s->..." property fetch can lead to false dead property. Make sure the variable type is known';

    public function __construct(
        private readonly Standard $standard,
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return PropertyFetch::class;
    }

    /**
     * @param PropertyFetch $node
     * @return mixed[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $callerType = $scope->getType($node->var);
        if (! $callerType instanceof MixedType) {
            return [];
        }

        $printedVar = $this->standard->prettyPrintExpr($node->var);

        return [sprintf(self::ERROR_MESSAGE, $printedVar)];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
function run($unknownType)
{
    return $unknownType->name;
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
function run(KnownType $knownType)
{
    return $knownType->name;
}
CODE_SAMPLE
            ),
        ]);
    }
}
