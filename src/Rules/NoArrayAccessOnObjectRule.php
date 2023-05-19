<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Matcher\ArrayStringAndFnMatcher;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoArrayAccessOnObjectRule\NoArrayAccessOnObjectRuleTest
 */
final class NoArrayAccessOnObjectRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit methods over array access on object';

    /**
     * @var array<class-string>
     */
    private const ALLOWED_CLASSES = ['SplFixedArray', 'SimpleXMLElement'];
    /**
     * @readonly
     * @var \Symplify\PHPStanRules\Matcher\ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    public function __construct(ArrayStringAndFnMatcher $arrayStringAndFnMatcher)
    {
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ArrayDimFetch::class;
    }

    /**
     * @param ArrayDimFetch $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $varStaticType = $scope->getType($node->var);

        $classNames = $varStaticType->getObjectClassNames();
        if ($classNames === []) {
            return [];
        }

        foreach ($classNames as $className) {
            if ($this->arrayStringAndFnMatcher->isMatchWithIsA($className, self::ALLOWED_CLASSES)) {
                return [];
            }
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run(MagicArrayObject $magicArrayObject)
    {
        return $magicArrayObject['more_magic'];
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run(MagicArrayObject $magicArrayObject)
    {
        return $magicArrayObject->getExplicitValue();
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
