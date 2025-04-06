<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony\ConfigClosure;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;

/**
 * @implements Rule<MethodCall>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoServiceSameNameSetClassRule\NoServiceSameNameSetClassRuleTest
 */
final class NoServiceSameNameSetClassRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'No need to duplicate service class and name. Use only "$services->set(%s::class)" instead';

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
        if (! is_string($serviceNameValue)) {
            return [];
        }

        $serviceTypeValue = NamingHelper::getName($serviceType->class);
        if (! is_string($serviceTypeValue)) {
            return [];
        }

        if ($serviceNameValue !== $serviceTypeValue) {
            return [];
        }

        if (strpos($serviceNameValue, '\\') !== false) {
            $serviceNameValue = Strings::after($serviceNameValue, '\\', -1);
        }

        $identifierRuleError = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $serviceNameValue))
            ->identifier(SymfonyRuleIdentifier::NO_SERVICE_SAME_NAME_SET_CLASS)
            ->build();

        return [$identifierRuleError];
    }
}
