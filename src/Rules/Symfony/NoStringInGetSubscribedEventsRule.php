<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\SymfonyClass;
use Symplify\PHPStanRules\Enum\SymfonyRuleIdentifier;

/**
 * @implements Rule<ClassMethod>
 */
final class NoStringInGetSubscribedEventsRule implements Rule
{
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
        if (! $classReflection->implementsInterface(SymfonyClass::EVENT_SUBSCRIBER_INTERFACE)) {
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
                if ($classConstFetch->class->toString() === SymfonyClass::FORM_EVENTS) {
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

            $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->identifier(SymfonyRuleIdentifier::NO_STRING_IN_GET_SUBSCRIBED_EVENTS)
                ->build();

            return [$ruleError];
        }

        return [];
    }
}
