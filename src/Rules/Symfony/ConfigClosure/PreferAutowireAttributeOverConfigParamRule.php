<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony\ConfigClosure;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Enum\SymfonyClass;
use Symplify\PHPStanRules\Enum\SymfonyFunctionName;
use Symplify\PHPStanRules\Helper\NamingHelper;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyClosureDetector;

/**
 * @implements Rule<Closure>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\PreferAutowireAttributeOverConfigParamRule\PreferAutowireAttributeOverConfigParamRuleTest
 */
final class PreferAutowireAttributeOverConfigParamRule implements Rule
{
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;
    /**
     * @api used in tests
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of parameter reference in config, add #[Autowire(param: ...)] in the "%s" class constructor';

    /**
     * @readonly
     */
    private NodeFinder $nodeFinder;

    public function __construct(
        ReflectionProvider $reflectionProvider
    ) {
        $this->reflectionProvider = $reflectionProvider;
        $this->nodeFinder = new NodeFinder();
    }

    public function getNodeType(): string
    {
        return Closure::class;
    }

    /**
     * @param Closure $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // enable this rule only if the autowire attribute is present
        if (! $this->reflectionProvider->hasClass(SymfonyClass::ATTRIBUTE)) {
            return [];
        }

        if (! SymfonyClosureDetector::detect($node)) {
            return [];
        }

        $methodCalls = $this->nodeFinder->findInstanceOf($node, MethodCall::class);

        $ruleErrors = [];

        foreach ($methodCalls as $methodCall) {
            if ($methodCall->isFirstClassCallable()) {
                continue;
            }

            if (! NamingHelper::isNames($methodCall->name, ['arg', 'args'])) {
                continue;
            }

            // find param() func call or string with '%' in it
            if (! $this->hasPossibleParameterInject($methodCall)) {
                continue;
            }

            // find out parent class! if in /vendor, let's skip it
            $serviceClassName = $this->resolveRegisteredServiceClassName($methodCall, $scope);
            if (! is_string($serviceClassName)) {
                continue;
            }

            // let's skip, as /vendor service that cannot be edited
            if ($this->isVendorClass($serviceClassName)) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $serviceClassName);

            $identifierRuleError = RuleErrorBuilder::message($errorMessage)
                ->identifier(SymfonyRuleIdentifier::PREFER_AUTOWIRE_ATTRIBUTE_OVER_CONFIG_PARAM)
                ->line($methodCall->getStartLine())
                ->build();

            $ruleErrors[] = $identifierRuleError;
        }

        return $ruleErrors;
    }

    private function hasPossibleParameterInject(MethodCall $methodCall): bool
    {
        foreach ($methodCall->getArgs() as $arg) {
            if ($this->isParamFuncOrString($arg->value)) {
                return true;
            }
        }

        return false;
    }

    private function isParamFuncOrString(Expr $expr): bool
    {
        $nodeFinder = new NodeFinder();

        /** @var FuncCall[] $funcCalls */
        $funcCalls = $nodeFinder->findInstanceOf($expr, FuncCall::class);

        foreach ($funcCalls as $funcCall) {
            if (NamingHelper::isName($funcCall->name, SymfonyFunctionName::PARAM)) {
                return true;
            }
        }

        /** @var String_[] $strings */
        $strings = $nodeFinder->findInstanceOf($expr, String_::class);
        foreach ($strings as $string) {
            if (strncmp($string->value, '%', strlen('%')) === 0) {
                return true;
            }
        }

        return false;
    }

    private function isVendorClass(string $className): bool
    {
        if (! $this->reflectionProvider->hasClass($className)) {
            return false;
        }

        $serviceClassReflection = $this->reflectionProvider->getClass($className);
        return strpos((string) $serviceClassReflection->getFileName(), '/vendor/') !== false;
    }

    private function resolveClassNameFromServiceSetMethodCall(MethodCall $setMethodCall, Scope $scope): ?string
    {
        $serviceSetMethodCallArgs = $setMethodCall->getArgs();

        // two params? then service class is the 2nd arg
        $serviceArg = $serviceSetMethodCallArgs[1] ?? $serviceSetMethodCallArgs[0];
        $serviceClassNameType = $scope->getType($serviceArg->value);

        if (! $serviceClassNameType instanceof ConstantStringType) {
            return null;
        }

        $serviceClassName = $serviceClassNameType->getValue();

        // probably only another service override
        if (! $this->reflectionProvider->hasClass($serviceClassName)) {
            return null;
        }

        return $serviceClassName;
    }

    private function resolveRegisteredServiceClassName(MethodCall $methodCall, Scope $scope): ?string
    {
        $currentMethodCall = $methodCall;

        while ($currentMethodCall instanceof MethodCall) {
            if (NamingHelper::isName($currentMethodCall->name, 'set')) {
                return $this->resolveClassNameFromServiceSetMethodCall($currentMethodCall, $scope);
            }

            $currentMethodCall = $currentMethodCall->var;
        }

        return null;
    }
}
