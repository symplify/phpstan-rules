<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

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
            if (strpos($constantString->getValue(), '\\Document\\') === false) {
                continue;
            }

            $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->identifier(RuleIdentifier::PHPUNIT_NO_DOCUMENT_MOCKING)
                ->build();

            return [$ruleError];
        }

        return [];
    }
}
