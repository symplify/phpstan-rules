<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\NoRequiredOutsideClassRule\NoRequiredOutsideClassRuleTest
 *
 * @implements Rule<Trait_>
 */
final class NoRequiredOutsideClassRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Symfony #[Require]/@required should be used only in classes to avoid misuse';

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
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $ruleErrors = [];

        foreach ($node->getMethods() as $classMethod) {
            if ($this->isAutowiredClassMethod($classMethod)) {
                $ruleErrors[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                    ->file($scope->getFile())
                    ->identifier(RuleIdentifier::SYMFONY_NO_REQUIRED_OUTSIDE_CLASS)
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
        return $docComment instanceof Doc && strpos($docComment->getText(), '@required') !== false;
    }
}
