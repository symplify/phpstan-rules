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
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Interface have suffix of "Interface", trait have "Trait" suffix exclusively', [
            new CodeSample(
                <<<'CODE_SAMPLE'
<?php

interface NotSuffixed
{
}

trait NotSuffixed
{
}

abstract class NotPrefixedClass
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
<?php

interface SuffixedInterface
{
}

trait SuffixedTrait
{
}

abstract class AbstractClass
{
}
CODE_SAMPLE
            ),
        ]);
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
     * @return list<RuleError>
     */
    private function processInterfaceSuffix(Identifier $identifier): array
    {
        if (substr_compare($identifier->toString(), 'Interface', -strlen('Interface')) === 0) {
            return [];
        }

        if (substr_compare($identifier->toString(), 'Trait', -strlen('Trait')) === 0) {
            return [RuleErrorBuilder::message(self::TRAIT_ERROR_MESSAGE)->build()];
        }

        return [RuleErrorBuilder::message(self::INTERFACE_ERROR_MESSAGE)->build()];
    }

    /**
     * @return list<RuleError>
     */
    private function processTraitSuffix(Identifier $identifier): array
    {
        if (substr_compare($identifier->toString(), 'Trait', -strlen('Trait')) === 0) {
            return [];
        }

        return [RuleErrorBuilder::message(self::TRAIT_ERROR_MESSAGE)->build()];
    }

    /**
     * @return list<RuleError>
     */
    private function processClassSuffix(Identifier $identifier, bool $isAbstract): array
    {
        if ($isAbstract && strncmp($identifier->toString(), 'Abstract', strlen('Abstract')) !== 0) {
            return [RuleErrorBuilder::message(self::ABSTRACT_ERROR_MESSAGE)->build()];
        }

        if (! $isAbstract && strncmp($identifier->toString(), 'Abstract', strlen('Abstract')) === 0) {
            return [RuleErrorBuilder::message(self::ABSTRACT_ERROR_MESSAGE)->build()];
        }

        if (substr_compare($identifier->toString(), 'Interface', -strlen('Interface')) === 0) {
            return [RuleErrorBuilder::message(self::INTERFACE_ERROR_MESSAGE)->build()];
        }

        if (substr_compare($identifier->toString(), 'Trait', -strlen('Trait')) === 0) {
            return [RuleErrorBuilder::message(self::TRAIT_ERROR_MESSAGE)->build()];
        }

        return [];
    }
}
