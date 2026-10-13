<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Only route actions - "*Action()" and "__invoke()" - can be public in a controller.
 *
 * Magic methods, #[Required] setters, methods required by a parent class or interface and event subscriber
 * listeners are skipped.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\NoPublicNonActionMethodInControllerRule\NoPublicNonActionMethodInControllerRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final class NoPublicNonActionMethodInControllerRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Controller method "%s()" is not a route action, so it cannot be public. Make it protected or private';

    /**
     * @var string
     */
    private const CONTROLLER_SUFFIX = 'Controller';

    /**
     * @var string
     */
    private const ACTION_SUFFIX = 'action';

    /**
     * @var string
     */
    private const REQUIRED_ATTRIBUTE = Required::class;

    /**
     * @var string
     */
    private const EVENT_SUBSCRIBER_INTERFACE = EventSubscriberInterface::class;

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->isPublic()) {
            return [];
        }

        // inside a trait the scope class is the controller using it, so the trait method would be checked repeatedly
        if ($scope->isInTrait()) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (substr_compare($classReflection->getName(), self::CONTROLLER_SUFFIX, -strlen(self::CONTROLLER_SUFFIX)) !== 0) {
            return [];
        }

        // listener methods are called by the event dispatcher
        if ($classReflection->implementsInterface(self::EVENT_SUBSCRIBER_INTERFACE)) {
            return [];
        }

        $methodName = $node->name->toString();
        if (strncmp($methodName, '__', strlen('__')) === 0) {
            return [];
        }

        $lowerMethodName = strtolower($methodName);
        if (substr_compare($lowerMethodName, self::ACTION_SUFFIX, -strlen(self::ACTION_SUFFIX)) === 0) {
            return [];
        }

        if ($this->hasRequiredAttribute($node) || $this->isInheritedContract($classReflection, $methodName)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $methodName);

        return [
            RuleErrorBuilder::message($errorMessage)
                ->identifier('symfony.noPublicNonActionMethodInController')
                ->build(),
        ];
    }

    private function hasRequiredAttribute(ClassMethod $classMethod): bool
    {
        foreach ($classMethod->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($attr->name->toString() === self::REQUIRED_ATTRIBUTE) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isInheritedContract(ClassReflection $classReflection, string $methodName): bool
    {
        $parentClassReflection = $classReflection->getParentClass();
        if ($parentClassReflection instanceof ClassReflection && $parentClassReflection->hasNativeMethod($methodName)) {
            return true;
        }

        foreach ($classReflection->getInterfaces() as $interfaceReflection) {
            if ($interfaceReflection->hasNativeMethod($methodName)) {
                return true;
            }
        }

        return false;
    }
}
