<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitClassSuffixesRule\ExplicitClassSuffixesRuleTest
 */
final class ExplicitClassSuffixesRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const INTERFACE_ERROR_MESSAGE = 'Interface must be suffixed with "Interface" exclusively';

    /**
     * @var string
     */
    public const TRAIT_ERROR_MESSAGE = 'Trait must be suffixed by "Trait" exclusively';

    public function getNodeType(): string
    {
        return ClassLike::class;
    }

    /**
     * @param ClassLike $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Identifier) {
            return [];
        }

        if ($node instanceof Interface_) {
            if (str_ends_with($node->name->toString(), 'Interface')) {
                return [];
            }

            return [self::INTERFACE_ERROR_MESSAGE];
        }

        if ($node instanceof Trait_) {
            if (str_ends_with($node->name->toString(), 'Trait')) {
                return [];
            }

            return [self::TRAIT_ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('...', [
            new CodeSample('...', '...'),
        ]);
    }
}
