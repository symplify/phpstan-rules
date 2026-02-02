<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use Symplify\PHPStanRules\Enum\RuleIdentifier\PHPUnitRuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;

/**
 * @implements Rule<MethodCall>
 * @see \Symplify\PHPStanRules\Tests\Rules\Doctrine\NoDocumentMockingRule\NoDocumentMockingRuleTest
 */
final readonly class NoDocumentMockingRule implements Rule
{
    public const string ERROR_MESSAGE = 'Instead of document mocking, create object directly to get better type support';

    public function __construct(
        private ReflectionProvider $reflectionProvider
    ) {
    }

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

        if (! NamingHelper::isNames($node->name, ['createMock', 'createStub', 'createConfiguredMock'])) {
            return [];
        }

        $firstArg = $node->getArgs()[0];
        $mockedClassType = $scope->getType($firstArg->value);
        foreach ($mockedClassType->getConstantStrings() as $constantStringType) {
            if (! str_contains($constantStringType->getValue(), '\\Document\\') && ! str_contains($constantStringType->getValue(), '\\Entity\\')) {
                continue;
            }

            if ($this->shouldSkipDocumentClass($constantStringType)) {
                continue;
            }

            $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->identifier(PHPUnitRuleIdentifier::NO_DOCUMENT_MOCKING)
                ->build();

            return [$ruleError];
        }

        return [];
    }

    private function shouldSkipDocumentClass(ConstantStringType $constantStringType): bool
    {
        if ($this->reflectionProvider->hasClass($constantStringType->getValue())) {
            $classReflection = $this->reflectionProvider->getClass($constantStringType->getValue());
            if ($classReflection->isAbstract()) {
                return true;
            }

            if ($classReflection->isInterface()) {
                return true;
            }
        }

        return false;
    }
}
