<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use Doctrine\ORM\Mapping\Entity;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\DoctrineRuleIdentifier;

/**
 * A Doctrine entity is hydrated via reflection without the constructor, so a readonly class breaks loading and
 * proxying. An entity is recognized by an #[ORM\Entity] attribute or a public static loadMetadata() method.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Doctrine\NoReadonlyEntityClassRule\NoReadonlyEntityClassRuleTest
 *
 * @implements Rule<Class_>
 */
final class NoReadonlyEntityClassRule implements Rule
{
    public const string ERROR_MESSAGE = 'Entity class "%s" must not be readonly. Doctrine hydrates entities via reflection without the constructor, which a readonly class forbids. Remove the readonly modifier from the class';

    private const string ENTITY_ATTRIBUTE = Entity::class;

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     *
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->isReadonly()) {
            return [];
        }

        if (! $node->name instanceof Identifier) {
            return [];
        }

        if (! $this->isEntity($node)) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(
            sprintf(self::ERROR_MESSAGE, (string) $node->namespacedName)
        )
            ->identifier(DoctrineRuleIdentifier::NO_READONLY_ENTITY_CLASS)
            ->build();

        return [$identifierRuleError];
    }

    private function isEntity(Class_ $class): bool
    {
        foreach ($class->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($attr->name->toString() === self::ENTITY_ATTRIBUTE) {
                    return true;
                }
            }
        }

        $loadMetadataMethod = $class->getMethod('loadMetadata');

        return $loadMetadataMethod instanceof ClassMethod && $loadMetadataMethod->isPublic();
    }
}
