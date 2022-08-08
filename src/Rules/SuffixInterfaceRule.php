<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\SuffixInterfaceRule\SuffixInterfaceRuleTest
 */
final class SuffixInterfaceRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Interface must be suffixed with "Interface" exclusively';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ClassLike::class;
    }

    /**
     * @param ClassLike $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (substr_compare((string) $node->name, 'Interface', -strlen('Interface')) === 0) {
            if (! $node instanceof Interface_) {
                return [self::ERROR_MESSAGE];
            }

            return [];
        }

        if ($node instanceof Interface_) {
            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
interface SomeClass
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
interface SomeInterface
{
}
CODE_SAMPLE
            ),
        ]);
    }
}
