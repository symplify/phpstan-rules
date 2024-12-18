<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use TomasVotruba\Handyman\PHPStan\DoctrineEntityDocumentAnalyser;

/**
 * The ORM entities and ODM documents should never be mocked, as it leads to typeless code.
 * Use them directly instead.
 *
 * @see \TomasVotruba\Handyman\Tests\PHPStan\Rule\NoEntityMockingRule\NoEntityMockingRuleTest
 * @implements Rule<MethodCall>
 */
final readonly class NoEntityMockingRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of entity or document mocking, create object directly to get better type support';

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
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->isCreateMockMethod($node)) {
            return [];
        }

        $firstArg = $node->getArgs()[0];
        $mockedClassType = $scope->getType($firstArg->value);

        foreach ($mockedClassType->getConstantStrings() as $constantStringType) {
            if (! $this->reflectionProvider->hasClass($constantStringType->getValue())) {
                continue;
            }

            $classReflection = $this->reflectionProvider->getClass($constantStringType->getValue());
            if (! DoctrineEntityDocumentAnalyser::isEntityClass($classReflection)) {
                continue;
            }

            $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->build();

            return [$ruleError];
        }

        return [];
    }

    private function isCreateMockMethod(MethodCall $methodCall): bool
    {
        if ($methodCall->isFirstClassCallable()) {
            return false;
        }

        if (! $methodCall->name instanceof Identifier) {
            return false;
        }

        $methodName = $methodCall->name->toString();
        return $methodName === 'createMock';
    }
}
