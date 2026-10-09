<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\SymfonyClass;

/**
 * A constructor dependency must be autowired by its interface, not by the concrete class.
 *
 * Typing "Router $router" locks the service to one implementation and blocks decoration, while
 * "RouterInterface $router" describes what the class actually needs. The interface is discovered by
 * convention: a param typed "X" is reported when "XInterface" exists and "X" implements it. Only
 * Symfony and Doctrine classes are checked - project classes are left alone.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\PreferInterfaceInConstructorRule\PreferInterfaceInConstructorRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final class PreferInterfaceInConstructorRule implements Rule
{
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;

    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Constructor dependency "%s" is typed as concrete "%s". Use the "%s" interface instead';

    /**
     * Only 3rd-party contracts are enforced - project classes are free to be typed directly.
     *
     * @var string[]
     */
    private const HANDLED_NAMESPACE_PREFIXES = ['Symfony\\', 'Doctrine\\'];

    /**
     * @var string[]
     */
    private const SKIPPED_CLASSES = [SymfonyClass::MAILER_TRANSPORT];

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     *
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->name->toLowerString() !== '__construct') {
            return [];
        }

        $ruleErrors = [];

        foreach ($node->params as $param) {
            if (! $param->type instanceof Name) {
                continue;
            }

            $className = $scope->resolveName($param->type);
            $interfaceName = $this->matchImplementedSameNamedInterface($className);
            if ($interfaceName === null) {
                continue;
            }

            $parameterName = $param->var instanceof Variable && is_string($param->var->name)
                ? '$' . $param->var->name
                : '';

            $ruleErrors[] = RuleErrorBuilder::message(
                sprintf(self::ERROR_MESSAGE, $parameterName, $className, $interfaceName)
            )
                ->identifier('symplify.preferInterfaceInConstructor')
                ->line($param->getStartLine())
                ->build();
        }

        return $ruleErrors;
    }

    /**
     * Returns the "<class>Interface" name when the class implements it, null otherwise.
     */
    private function matchImplementedSameNamedInterface(string $className): ?string
    {
        if (! $this->isHandledNamespace($className)) {
            return null;
        }

        if (in_array($className, self::SKIPPED_CLASSES, true)) {
            return null;
        }

        if (! $this->reflectionProvider->hasClass($className)) {
            return null;
        }

        $classReflection = $this->reflectionProvider->getClass($className);
        if ($classReflection->isInterface()) {
            return null;
        }

        $interfaceName = $className . 'Interface';
        if (! $this->reflectionProvider->hasClass($interfaceName)) {
            return null;
        }

        if (! $this->reflectionProvider->getClass($interfaceName)->isInterface()) {
            return null;
        }

        if (! $classReflection->implementsInterface($interfaceName)) {
            return null;
        }

        return $interfaceName;
    }

    private function isHandledNamespace(string $className): bool
    {
        $found = false;
        foreach (self::HANDLED_NAMESPACE_PREFIXES as $handledNamespacePrefix) {
            if (strncmp($className, $handledNamespacePrefix, strlen($handledNamespacePrefix)) === 0) {
                $found = true;
                break;
            }
        }

        return $found;
    }
}
