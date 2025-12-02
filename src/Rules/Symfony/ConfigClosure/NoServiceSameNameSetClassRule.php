<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony\ConfigClosure;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyClosureDetector;

/**
 * @implements Rule<Closure>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoServiceSameNameSetClassRule\NoServiceSameNameSetClassRuleTest
 */
final class NoServiceSameNameSetClassRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'No need to duplicate service class and name. Use only "$services->set(%s::class)" instead';

    /**
     * @readonly
     */
    private NodeFinder $nodeFinder;

    public function __construct()
    {
        $this->nodeFinder = new NodeFinder();
    }

    public function getNodeType(): string
    {
        return Closure::class;
    }

    /**
     * @param Closure $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! SymfonyClosureDetector::detect($node)) {
            return [];
        }

        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->nodeFinder->findInstanceOf($node, MethodCall::class);

        $ruleErrors = [];

        foreach ($methodCalls as $methodCall) {
            if ($methodCall->isFirstClassCallable()) {
                continue;
            }

            if (! NamingHelper::isName($methodCall->var, 'services')) {
                continue;
            }

            if (! NamingHelper::isName($methodCall->name, 'set')) {
                continue;
            }

            $serviceNameValue = $this->matchTwoArgsOfSameClassConstName($methodCall);
            if (! is_string($serviceNameValue)) {
                continue;
            }

            if (strpos($serviceNameValue, '\\') !== false) {
                $serviceNameValue = Strings::after($serviceNameValue, '\\', -1);
            }

            $identifierRuleError = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $serviceNameValue))
                ->identifier(SymfonyRuleIdentifier::NO_SERVICE_SAME_NAME_SET_CLASS)
                ->line($methodCall->getStartLine())
                ->build();

            $ruleErrors[] = $identifierRuleError;
        }

        return $ruleErrors;
    }

    /**
     * We look for:
     *
     * $services->set(SomeClass::class, SomeClass::class)
     */
    private function matchTwoArgsOfSameClassConstName(MethodCall $methodCall): ?string
    {
        if (count($methodCall->getArgs()) !== 2) {
            return null;
        }

        $serviceName = $methodCall->getArgs()[0]->value;
        $serviceType = $methodCall->getArgs()[1]->value;

        if (! $serviceName instanceof ClassConstFetch) {
            return null;
        }

        if (! $serviceType instanceof ClassConstFetch) {
            return null;
        }

        $serviceNameValue = NamingHelper::getName($serviceName->class);
        if (! is_string($serviceNameValue)) {
            return null;
        }

        $serviceTypeValue = NamingHelper::getName($serviceType->class);
        if (! is_string($serviceTypeValue)) {
            return null;
        }

        if ($serviceNameValue !== $serviceTypeValue) {
            return null;
        }

        return $serviceNameValue;
    }
}
