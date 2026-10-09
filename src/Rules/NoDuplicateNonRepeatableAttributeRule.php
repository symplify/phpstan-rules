<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Attribute;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * An attribute can only be repeated on the same class, method or property when it is
 * declared with the \Attribute::IS_REPEATABLE flag. Report every non-repeatable one used twice.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\NoDuplicateNonRepeatableAttributeRule\NoDuplicateNonRepeatableAttributeRuleTest
 *
 * @implements Rule<Node\Stmt>
 */
final class NoDuplicateNonRepeatableAttributeRule implements Rule
{
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;

    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Attribute "#[%s]" is used %d times on the same %s, but is not repeatable. Add the ' . Attribute::class . '::IS_REPEATABLE flag to its #[' . Attribute::class . '] declaration, or remove the duplicate';

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string
    {
        return Stmt::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $elementType = $this->resolveElementType($node);
        if ($elementType === null) {
            return [];
        }

        /** @var Class_|ClassMethod|Property $node */
        $countByAttribute = [];
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                $attributeName = $attr->name->toString();
                $countByAttribute[$attributeName] = ($countByAttribute[$attributeName] ?? 0) + 1;
            }
        }

        $ruleErrors = [];
        foreach ($countByAttribute as $attributeName => $count) {
            if ($count < 2) {
                continue;
            }

            if ($this->isRepeatable($attributeName)) {
                continue;
            }

            $ruleErrors[] = RuleErrorBuilder::message(
                sprintf(self::ERROR_MESSAGE, $attributeName, $count, $elementType)
            )
                ->identifier('symplify.noDuplicateNonRepeatableAttribute')
                ->build();
        }

        return $ruleErrors;
    }

    private function resolveElementType(Node $node): ?string
    {
        if ($node instanceof Class_) {
            return 'class';
        }

        if ($node instanceof ClassMethod) {
            return 'method';
        }

        if ($node instanceof Property) {
            return 'property';
        }

        return null;
    }

    private function isRepeatable(string $attributeName): bool
    {
        if (! $this->reflectionProvider->hasClass($attributeName)) {
            // cannot confirm it is non-repeatable, so stay silent to avoid a false positive
            return true;
        }

        $nativeReflection = $this->reflectionProvider->getClass($attributeName)
            ->getNativeReflection();
        foreach (method_exists($nativeReflection, 'getAttributes') ? $nativeReflection->getAttributes(Attribute::class) : [] as $reflectionAttribute) {
            $flags = $reflectionAttribute->getArguments()[0] ?? 0;

            return (bool) ($flags & Attribute::IS_REPEATABLE);
        }

        // the class has no #[\Attribute] declaration, treat as non-repeatable
        return false;
    }
}
