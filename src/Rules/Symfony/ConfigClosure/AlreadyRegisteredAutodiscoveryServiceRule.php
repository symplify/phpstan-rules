<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony\ConfigClosure;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Symfony\ConfigClosure\SymfonyClosureServicesExcludeResolver;
use Symplify\PHPStanRules\Symfony\ConfigClosure\SymfonyClosureServicesLoadResolver;
use Symplify\PHPStanRules\Symfony\ConfigClosure\SymfonyClosureServicesSetClassesResolver;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyClosureDetector;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\AlreadyRegisteredAutodiscoveryServiceRule\AlreadyRegisteredAutodiscoveryServiceRuleTest
 *
 * @implements Rule<Closure>
 */
final class AlreadyRegisteredAutodiscoveryServiceRule implements Rule
{
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'The "%s" service is already registered via autodiscovery ->load(), no need to set it twice';

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
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

        // 1. collect all load("X") namespaces
        $loadedServiceNamespaces = SymfonyClosureServicesLoadResolver::resolve($node);
        if ($loadedServiceNamespaces === []) {
            return [];
        }

        // 2. check all bare $services->set("Y");
        $standaloneSetServicesToLines = SymfonyClosureServicesSetClassesResolver::resolve($node);
        if ($standaloneSetServicesToLines === []) {
            return [];
        }

        // 3. collect all $services->load()->exclude([...]); paths
        $excludedPaths = SymfonyClosureServicesExcludeResolver::resolve($node, $scope);

        $twiceRegisteredServices = $this->findTwiceRegisteredServices($standaloneSetServicesToLines, $loadedServiceNamespaces);

        // filter out excluded paths
        $twiceRegisteredServices = $this->filterOutExcludedPaths($twiceRegisteredServices, $excludedPaths);

        if ($twiceRegisteredServices === []) {
            return [];
        }

        $ruleErrors = [];

        foreach ($twiceRegisteredServices as $serviceClass => $line) {
            $errorMessage = sprintf(self::ERROR_MESSAGE, $serviceClass);

            $identifierRuleError = RuleErrorBuilder::message($errorMessage)
                ->identifier(SymfonyRuleIdentifier::ALREADY_REGISTERED_AUTODISCOVERY_SERVICE)
                ->line($line)
                ->build();

            $ruleErrors[] = $identifierRuleError;
        }

        return $ruleErrors;
    }

    /**
     * @param array<string, int> $standaloneSetServicesToLines
     * @param array<string> $loadedServiceNamespaces
     *
     * @return array<string, int>
     */
    private function findTwiceRegisteredServices(array $standaloneSetServicesToLines, array $loadedServiceNamespaces): array
    {
        $twiceRegisteredServices = [];

        foreach ($standaloneSetServicesToLines as $serviceClass => $line) {
            foreach ($loadedServiceNamespaces as $loadedServiceNamespace) {
                if (strncmp($serviceClass, $loadedServiceNamespace, strlen($loadedServiceNamespace)) === 0) {
                    $twiceRegisteredServices[$serviceClass] = $line;
                    continue 2;
                }
            }
        }

        return $twiceRegisteredServices;
    }

    /**
     * @param array<string, int> $servicesToLine
     * @param array<string> $excludedPaths
     *
     * @return array<string, int>
     */
    private function filterOutExcludedPaths(array $servicesToLine, array $excludedPaths): array
    {
        foreach (array_keys($servicesToLine) as $serviceClass) {
            if ($this->isClassInExcludedPaths($serviceClass, $excludedPaths)) {
                unset($servicesToLine[$serviceClass]);
            }
        }

        return $servicesToLine;
    }

    private function resolveServiceFilePath(string $className): ?string
    {
        if (! $this->reflectionProvider->hasClass($className)) {
            return null;
        }

        $classReflection = $this->reflectionProvider->getClass($className);
        return $classReflection->getFileName();
    }

    /**
     * @param string[] $excludedPaths
     */
    private function isClassInExcludedPaths(string $serviceClass, array $excludedPaths): bool
    {
        $serviceFilePath = $this->resolveServiceFilePath($serviceClass);
        if (! is_string($serviceFilePath)) {
            return false;
        }

        foreach ($excludedPaths as $excludedPath) {
            if (strncmp($serviceFilePath, $excludedPath, strlen($excludedPath)) === 0) {
                return true;
            }
        }

        return false;
    }
}
