<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use Symplify\PHPStanRules\Enum\SymfonyClass;
use Symplify\PHPStanRules\Enum\SymfonyRuleIdentifier;

/**
 * @implements Rule<Class_>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\FormTypeClassNameRule\FormTypeClassNameRuleTest
 */
final class FormTypeClassNameRule implements Rule
{
    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->namespacedName instanceof Name) {
            return [];
        }

        // all good
        $className = $node->namespacedName->toString();
        if (substr_compare($className, 'FormType', -strlen('FormType')) === 0) {
            return [];
        }

        $currentObjectType = new ObjectType($className);

        $parentObjectType = new ObjectType(SymfonyClass::FORM_TYPE);
        if (! $parentObjectType->isSuperTypeOf($currentObjectType)->yes()) {
            return [];
        }

        $errorMessage = sprintf(
            'Class extends "%s" must have "FormType" suffix to make form explicit, "%s" given',
            SymfonyClass::FORM_TYPE,
            $className
        );

        $identifierRuleError = RuleErrorBuilder::message($errorMessage)
            ->identifier(SymfonyRuleIdentifier::FORM_TYPE_CLASS_NAME)
            ->build();

        return [$identifierRuleError];
    }
}
