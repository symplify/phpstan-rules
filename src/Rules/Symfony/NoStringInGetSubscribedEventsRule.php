<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use TomasVotruba\Handyman\Enum\ClassName;

/**
 * @implements Rule<ClassMethod>
 */
final class NoStringInGetSubscribedEventsRule implements Rule
{
    /**
     * @var string
     */
    private const EVENT_SUBSCRIBER_INTERFACE = 'Symfony\Component\EventDispatcher\EventSubscriberInterface';

    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Symfony getSubscribedEvents() method must contain only event class references, no strings';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->stmts === null) {
            return [];
        }

        if ($node->name->toString() !== 'getSubscribedEvents') {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        // only handle symfony one
        if (! $classReflection->implementsInterface(self::EVENT_SUBSCRIBER_INTERFACE)) {
            return [];
        }

        $nodeFinder = new NodeFinder();

        /** @var ArrayItem[] $arrayItems */
        $arrayItems = $nodeFinder->findInstanceOf($node->stmts, ArrayItem::class);

        foreach ($arrayItems as $arrayItem) {
            if (! $arrayItem->key instanceof Expr) {
                continue;
            }

            // must be class const fetch
            if ($arrayItem->key instanceof ClassConstFetch) {
                $classConstFetch = $arrayItem->key;

                if ($classConstFetch->class instanceof Expr) {
                    continue;
                }

                // skip Symfony FormEvents::class
                if ($classConstFetch->class->toString() === ClassName::FORM_EVENTS) {
                    continue;
                }

                if ($classConstFetch->name instanceof Expr) {
                    continue;
                }

                if ($classConstFetch->name->toString() === 'class') {
                    continue;
                }

                continue;
            }

            $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)->build();
            return [$ruleError];
        }

        return [];
    }
}
