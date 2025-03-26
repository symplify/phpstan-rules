<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Doctrine\DoctrineEventSubscriberAnalyzer;
use Symplify\PHPStanRules\Enum\SymfonyClass;
use Symplify\PHPStanRules\Enum\SymfonyRuleIdentifier;

/**
 * Based on https://tomasvotruba.com/blog/2019/07/22/how-to-convert-listeners-to-subscribers-and-reduce-your-configs
 * Subscribers have much better PHP support - IDE, PHPStan + Rector - than simple yaml files
 *
 * @implements Rule<InClassNode>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\NoListenerWithoutContractRule\NoListenerWithoutContractRuleTest
 */
final class NoListenerWithoutContractRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'There should be no listeners modified in config. Use EventSubscriberInterface contract or #[AsEventListener] attribute and native PHP instead';

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $scope->isInClass()) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (substr_compare($classReflection->getName(), 'Listener', -strlen('Listener')) !== 0) {
            return [];
        }

        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        if ($classLike->implements !== []) {
            return [];
        }

        // is invokable listeners?
        if ($classLike->getMethod('__invoke') instanceof ClassMethod) {
            return [];
        }

        if (DoctrineEventSubscriberAnalyzer::detect($classLike)) {
            return [];
        }

        if ($this->isSecurityListener($classLike)) {
            return [];
        }

        if ($this->hasAsListenerAttribute($classLike)) {
            return [];
        }

        if ($this->isFormEventsListener($classLike)) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(SymfonyRuleIdentifier::NO_LISTENER_WITHOUT_CONTRACT)
            ->build();

        return [$identifierRuleError];
    }

    private function hasAsListenerAttribute(Class_ $class): bool
    {
        foreach ($class->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($attr->name->toString() === SymfonyClass::EVENT_LISTENER_ATTRIBUTE) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Form listeners are often registered manually in code and don't need any specific hooks
     */
    private function isFormEventsListener(Class_ $class): bool
    {
        foreach ($class->getMethods() as $classMethod) {
            if (! $classMethod->isPublic()) {
                continue;
            }

            foreach ($classMethod->params as $param) {
                if ($param->type instanceof Name && strncmp($param->type->toString(), 'Symfony\Component\Form\Event\\', strlen('Symfony\Component\Form\Event\\')) === 0) {

                    return true;
                }
            }
        }

        return false;
    }

    private function isSecurityListener(Class_ $class): bool
    {
        if (! $class->extends instanceof Name) {
            return false;
        }

        return in_array($class->extends->toString(), [SymfonyClass::SECURITY_LISTENER, SymfonyClass::FORM_SECURITY_LISTENER], true);
    }
}
