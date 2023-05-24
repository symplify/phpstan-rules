<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ExplicitClassSuffixesRule implements Rule, DocumentedRuleInterface
{
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();

        dump($classReflection->isClass());

        dump($classReflection->isInterface());

        dd($classReflection->isTrait());
    }

    public function getRuleDefinition(): RuleDefinition
    {
        $ruleDefinition = new RuleDefinition('...', [
            new CodeSample('...', '...'),
        ]);

        return $ruleDefinition;
    }
}
