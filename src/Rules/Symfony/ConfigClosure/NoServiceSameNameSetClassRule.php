<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony\ConfigClosure;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Helper\NamingHelper;

/**
 * @implements Rule<MethodCall>
 */
final class NoServiceSameNameSetClassRule implements Rule
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'No need to duplicate service class and name. Use only $services->set(%s) instead';

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
        if ($node->isFirstClassCallable()) {
            return [];
        }

        if (! NamingHelper::isName($node->name, 'set')) {
            return [];
        }

        if (count($node->getArgs()) !== 2) {
            return [];
        }

        $serviceName = $node->getArgs()[0]->value;
        $serviceType = $node->getArgs()[1]->value;

        if (! $serviceName instanceof ClassConstFetch) {
            return [];
        }

        if (! $serviceType instanceof ClassConstFetch) {
            return [];
        }

        $serviceNameValue = NamingHelper::getName($serviceName->class);
        $serviceTypeValue = NamingHelper::getName($serviceType->class);

        if ($serviceNameValue !== $serviceTypeValue) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $serviceNameValue))
            ->identifier('symfony.noServiceSameNameSetClass')
            ->build();

        return [$identifierRuleError];
    }
}
