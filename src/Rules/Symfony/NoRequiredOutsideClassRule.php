<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\PHPStan\Rule;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @see \TomasVotruba\Handyman\Tests\PHPStan\Rule\NoRequiredOutsideClassRule\NoRequiredOutsideClassRuleTest
 *
 * @implements Rule<Trait_>
 */
final class NoRequiredOutsideClassRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Symfony #[Require]/@required should be used only in classes to avoid missuse';

    /**
     * @var string
     */
    private const REQUIRED_ATTRIBUTE = 'Symfony\Contracts\Service\Attribute\Required';

    public function getNodeType(): string
    {
        return Trait_::class;
    }

    /**
     * @param Trait_ $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $ruleErrors = [];

        foreach ($node->getMethods() as $classMethod) {
            if ($this->isAutowiredClassMethod($classMethod)) {
                $ruleErrors[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                    ->file($scope->getFile())
                    ->line($classMethod->getLine())
                    ->build();
            }
        }

        return $ruleErrors;
    }

    private function isAutowiredClassMethod(ClassMethod $classMethod): bool
    {
        foreach ($classMethod->getAttrGroups() as $attributeGroup) {
            foreach ($attributeGroup->attrs as $attr) {
                if ($attr->name->toString() === self::REQUIRED_ATTRIBUTE) {
                    return true;
                }
            }
        }

        $docComment = $classMethod->getDocComment();
        return $docComment instanceof Doc && str_contains($docComment->getText(), '@required');
    }
}
