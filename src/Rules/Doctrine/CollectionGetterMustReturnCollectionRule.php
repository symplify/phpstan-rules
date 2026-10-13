<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Getter returning a to-many entity association must declare "Collection" return type.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionGetterMustReturnCollectionRule\CollectionGetterMustReturnCollectionRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final class CollectionGetterMustReturnCollectionRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method "%s()" returns to-many association "$%s", so it must declare "%s" return type';

    /**
     * @var array<string>
     */
    private const TO_MANY_ATTRIBUTES = [ManyToMany::class, OneToMany::class];

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $returnType = $node->returnType instanceof NullableType ? $node->returnType->type : $node->returnType;

        if ($returnType instanceof Name && $returnType->toString() === Collection::class) {
            return [];
        }

        if ($node->stmts === null || ! $scope->isInClass()) {
            return [];
        }

        $nativeReflection = $scope->getClassReflection()
            ->getNativeReflection();

        $nodeFinder = new NodeFinder();

        /** @var Return_[] $returns */
        $returns = $nodeFinder->findInstanceOf($node->stmts, Return_::class);

        foreach ($returns as $return) {
            if (! $return->expr instanceof PropertyFetch) {
                continue;
            }

            if (! $return->expr->var instanceof Variable || $return->expr->var->name !== 'this') {
                continue;
            }

            if (! $return->expr->name instanceof Identifier) {
                continue;
            }

            $propertyName = $return->expr->name->toString();
            if (! $nativeReflection->hasProperty($propertyName)) {
                continue;
            }

            $hasToManyAttribute = false;
            foreach ($nativeReflection->getProperty($propertyName)->getAttributes() as $attribute) {
                if (in_array($attribute->getName(), self::TO_MANY_ATTRIBUTES, true)) {
                    $hasToManyAttribute = true;
                    break;
                }
            }

            if (! $hasToManyAttribute) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $node->name->toString(), $propertyName, Collection::class);

            return [
                RuleErrorBuilder::message($errorMessage)
                    ->identifier('doctrine.collectionGetterMustReturnCollection')
                    ->line($node->getStartLine())
                    ->build(),
            ];
        }

        return [];
    }
}
