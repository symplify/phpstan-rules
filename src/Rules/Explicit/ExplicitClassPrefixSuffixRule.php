<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use function str_ends_with;

/**
 * @implements Rule<ClassLike>
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitClassPrefixSuffixRule\ExplicitClassPrefixSuffixRuleTest
 */
final class ExplicitClassPrefixSuffixRule implements Rule
{
    /**
     * @api
     * @var string
     */
    public const INTERFACE_ERROR_MESSAGE = 'Interface must be suffixed with "Interface" exclusively';

    /**
     * @api
     * @var string
     */
    public const TRAIT_ERROR_MESSAGE = 'Trait must be suffixed by "Trait" exclusively';

    /**
     * @api
     * @var string
     */
    public const ABSTRACT_ERROR_MESSAGE = 'Abstract class must be prefixed by "Abstract" exclusively';

    public function getNodeType(): string
    {
        return ClassLike::class;
    }

    /**
     * @param ClassLike $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier) {
            return [];
        }

        if ($node instanceof Interface_) {
            return $this->processInterfaceSuffix($node->name);
        }

        if ($node instanceof Trait_) {
            return $this->processTraitSuffix($node->name);
        }

        if ($node instanceof Class_) {
            return $this->processClassSuffix($node->name, $node->isAbstract());
        }

        return [];
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function processInterfaceSuffix(Identifier $identifier): array
    {
        if (str_ends_with($identifier->toString(), 'Interface')) {
            return [];
        }

        if (str_ends_with($identifier->toString(), 'Trait')) {
            return [RuleErrorBuilder::message(self::TRAIT_ERROR_MESSAGE)
                ->identifier(RuleIdentifier::EXPLICIT_TRAIT_SUFFIX_NAME)
                ->build()];
        }

        return [RuleErrorBuilder::message(self::INTERFACE_ERROR_MESSAGE)
            ->identifier(RuleIdentifier::EXPLICIT_INTERFACE_SUFFIX_NAME)
            ->build()];
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function processTraitSuffix(Identifier $identifier): array
    {
        if (str_ends_with($identifier->toString(), 'Trait')) {
            return [];
        }

        return [RuleErrorBuilder::message(self::TRAIT_ERROR_MESSAGE)
            ->identifier(RuleIdentifier::EXPLICIT_TRAIT_SUFFIX_NAME)
            ->build()];
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function processClassSuffix(Identifier $identifier, bool $isAbstract): array
    {
        if ($isAbstract && ! str_starts_with($identifier->toString(), 'Abstract')) {
            return [RuleErrorBuilder::message(self::ABSTRACT_ERROR_MESSAGE)
                ->identifier(RuleIdentifier::EXPLICIT_ABSTRACT_PREFIX_NAME)
                ->build()];
        }

        if (! $isAbstract && str_starts_with($identifier->toString(), 'Abstract')) {
            return [RuleErrorBuilder::message(self::ABSTRACT_ERROR_MESSAGE)
                ->identifier(RuleIdentifier::EXPLICIT_ABSTRACT_PREFIX_NAME)
                ->build(),
            ];
        }

        if (str_ends_with($identifier->toString(), 'Interface')) {
            return [RuleErrorBuilder::message(self::INTERFACE_ERROR_MESSAGE)
                ->identifier(RuleIdentifier::EXPLICIT_INTERFACE_SUFFIX_NAME)
                ->build()];
        }

        if (str_ends_with($identifier->toString(), 'Trait')) {
            return [RuleErrorBuilder::message(self::TRAIT_ERROR_MESSAGE)
                ->identifier(RuleIdentifier::EXPLICIT_TRAIT_SUFFIX_NAME)
                ->build()];
        }

        return [];
    }
}
