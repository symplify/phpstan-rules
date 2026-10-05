<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Rector\Rector\AbstractRector;
use Symplify\PHPStanRules\Enum\RuleIdentifier\RectorRuleIdentifier;

/**
 * @implements Rule<MethodCall>
 *
 * @đee \Symplify\PHPStanRules\Tests\Rules\Rector\PreferDirectIsNameRule\PreferDirectIsNameRuleTest
 */
final class PreferDirectIsNameRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use direct $this->isName() instead of fetching NodeNameResolver service';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->isFirstClassCallable()) {
            return [];
        }

        if (! $node->name instanceof Identifier) {
            return [];
        }

        if (! in_array($node->name, ['isName', 'isNames', 'getName'])) {
            return [];
        }

        if ($this->shouldSkipClassReflection($scope)) {
            return [];
        }

        if (! $node->var instanceof PropertyFetch) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RectorRuleIdentifier::PREFER_DIRECT_IS_NAME)
            ->build();

        return [$identifierRuleError];
    }

    private function shouldSkipClassReflection(Scope $scope): bool
    {
        if (! $scope->isInClass()) {
            return true;
        }

        $classReflection = $scope->getClassReflection();

        // skip self
        if ($classReflection->getName() === AbstractRector::class) {
            return true;
        }

        // check rector rules only
        if (! $classReflection->is(AbstractRector::class)) {
            return true;
        }

        // check child Rectors only
        return $classReflection->isAbstract();
    }
}
