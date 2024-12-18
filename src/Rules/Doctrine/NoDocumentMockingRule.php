<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<MethodCall>
 */
final class NoDocumentMockingRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of document mocking, create object directly to get better type support';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->isFirstClassCallable()) {
            return [];
        }

        if (! $node->name instanceof Identifier) {
            return [];
        }

        $methodName = $node->name->toString();
        if ($methodName !== 'createMock') {
            return [];
        }

        $firstArg = $node->getArgs()[0];
        $mockedClassType = $scope->getType($firstArg->value);
        foreach ($mockedClassType->getConstantStrings() as $constantString) {
            if (! str_contains($constantString->getValue(), '\\Document\\')) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }
}
