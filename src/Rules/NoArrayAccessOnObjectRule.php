<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\ObjectType;
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
     * @var string[]
     */
    private const ALLOWED_CLASSES = ['SplFixedArray', 'SimpleXMLElement', 'Iterator', 'Aws\ResultInterface', 'Symfony\Component\Form\FormInterface'];

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
        $varType = $scope->getType($node->var);
        if (! $varType instanceof ObjectType) {
            return [];
        }

        foreach (self::ALLOWED_CLASSES as $allowedClass) {
            if ($varType->isInstanceOf($allowedClass)->yes()) {
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
