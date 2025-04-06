<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;

/**
 * @implements Rule<ClassMethod>
 */
final class NoValueObjectInServiceConstructorRule implements Rule
{
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! NamingHelper::isName($node->name, '__construct')) {
            return [];
        }

        if (! $scope->isInClass()) {
            return [];
        }

        $classReflection = $scope->getClassReflection();

        // value objects can accept value objects
        if ($this->isValueObject($classReflection->getName())) {
            return [];
        }

        $ruleErrors = [];

        foreach ($node->params as $param) {
            if (! $param->type instanceof Name) {
                continue;
            }

            $paramType = $param->type->toString();
            if (! $this->isValueObject($paramType)) {
                continue;
            }

            $ruleErrors[] = RuleErrorBuilder::message(sprintf(
                'Value object "%s" cannot be passed to constructor of a service. Pass it as a method argument instead',
                $paramType
            ))
                ->identifier(RuleIdentifier::NO_VALUE_OBJECT_IN_SERVICE_CONSTRUCTOR)
                ->build();
        }

        return $ruleErrors;
    }

    private function isValueObject(string $className): bool
    {
        return preg_match('#(ValueObject|DataObject|Models)#', $className) === 1;
    }
}
