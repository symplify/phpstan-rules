<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\PHPUnitRuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;

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

        if (! NamingHelper::isName($node->name, 'createMock')) {
            return [];
        }

        $firstArg = $node->getArgs()[0];
        $mockedClassType = $scope->getType($firstArg->value);
        foreach ($mockedClassType->getConstantStrings() as $constantString) {
            if (strpos($constantString->getValue(), '\\Document\\') === false) {
                continue;
            }

            $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->identifier(PHPUnitRuleIdentifier::NO_DOCUMENT_MOCKING)
                ->build();

            return [$ruleError];
        }

        return [];
    }
}
