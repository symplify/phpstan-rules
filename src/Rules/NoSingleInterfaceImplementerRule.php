<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Arrays;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Collector\ImplementedInterfaceCollector;
use Symplify\PHPStanRules\Collector\InterfaceCollector;
use Symplify\PHPStanRules\Collector\InterfaceOfAbstractClassCollector;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoSingleInterfaceImplementerRule\NoSingleInterfaceImplementerRuleTest
 */
final class NoSingleInterfaceImplementerRule implements Rule
{
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;
    /**
     * @api used in test
     * @var string
     */
    public const ERROR_MESSAGE = 'Interface "%s" has only single implementer. Consider using the class directly as there is no point in using the interface.';

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $implementedInterfaces = Arrays::flatten($node->get(ImplementedInterfaceCollector::class));
        $interfaces = Arrays::flatten($node->get(InterfaceCollector::class));
        $interfacesOfAbstractClass = Arrays::flatten($node->get(InterfaceOfAbstractClassCollector::class));

        $onceUsedInterfaces = $this->resolveOnceUsedInterfaces($implementedInterfaces);
        $onceImplementedInterfaces = array_intersect($onceUsedInterfaces, $interfaces);

        // remove the abstract class implemented, as required transitionally
        $onceImplementedInterfaces = array_diff($onceImplementedInterfaces, $interfacesOfAbstractClass);

        if ($onceImplementedInterfaces === []) {
            return [];
        }

        $ruleErrors = [];
        foreach ($onceImplementedInterfaces as $onceImplementedInterface) {
            $interfaceReflection = $this->reflectionProvider->getClass($onceImplementedInterface);

            // most likely internal
            if ($interfaceReflection->getFileName() === null) {
                continue;
            }

            $ruleErrors[] = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $onceImplementedInterface))
                ->file($interfaceReflection->getFileName())
                ->identifier(RuleIdentifier::NO_SINGLE_INTERFACE_IMPLEMENTER)
                ->build();
        }

        return $ruleErrors;
    }

    /**
     * @param string[] $implementedInterfaces
     * @return string[]
     */
    private function resolveOnceUsedInterfaces(array $implementedInterfaces): array
    {
        $onceUsedInterfaces = [];

        $implementedInterfacesToCount = array_count_values($implementedInterfaces);
        foreach ($implementedInterfacesToCount as $interfaceName => $countUsed) {
            if ($countUsed !== 1) {
                continue;
            }

            $onceUsedInterfaces[] = $interfaceName;
        }

        return $onceUsedInterfaces;
    }
}
