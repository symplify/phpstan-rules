<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Collector\ClassConstantDeclarationCollector;
use Symplify\PHPStanRules\Collector\StaticClassConstFetchCollector;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenStaticClassConstFetchRule\ForbiddenStaticClassConstFetchRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final class ForbiddenStaticClassConstFetchRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid static access of constants, as they can change value. Use interface and contract method instead';

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $node
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        [$constantNamesByClass, $parentClassByClass] = $this->resolveClassMaps($node);

        /** @var array<string, list<array{string, string, int}>> $fetchesByFilePath */
        $fetchesByFilePath = $node->get(StaticClassConstFetchCollector::class);

        $ruleErrors = [];

        foreach ($fetchesByFilePath as $filePath => $fetches) {
            foreach ($fetches as [$className, $constantName, $line]) {
                // a child class overriding the constant makes late static binding meaningful, so keep it
                if ($this->isOverriddenInChildClass($className, $constantName, $constantNamesByClass, $parentClassByClass)) {
                    continue;
                }

                $ruleErrors[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                    ->identifier('symplify.forbiddenStaticClassConstFetch')
                    ->file($filePath)
                    ->line($line)
                    ->build();
            }
        }

        return $ruleErrors;
    }

    /**
     * @return array{array<string, string[]>, array<string, string|null>} constant names and parent class per class
     */
    private function resolveClassMaps(CollectedDataNode $collectedDataNode): array
    {
        /** @var array<string, list<array{string, string|null, string[]}>> $declarationsByFilePath */
        $declarationsByFilePath = $collectedDataNode->get(ClassConstantDeclarationCollector::class);

        $constantNamesByClass = [];
        $parentClassByClass = [];

        foreach ($declarationsByFilePath as $declarations) {
            foreach ($declarations as [$className, $parentClassName, $constantNames]) {
                $constantNamesByClass[$className] = $constantNames;
                $parentClassByClass[$className] = $parentClassName;
            }
        }

        return [$constantNamesByClass, $parentClassByClass];
    }

    /**
     * @param array<string, string[]>     $constantNamesByClass
     * @param array<string, string|null> $parentClassByClass
     */
    private function isOverriddenInChildClass(
        string $parentClassName,
        string $constantName,
        array $constantNamesByClass,
        array $parentClassByClass
    ): bool {
        foreach ($constantNamesByClass as $declaringClassName => $constantNames) {
            if ($declaringClassName === $parentClassName) {
                continue;
            }

            if (! in_array($constantName, $constantNames, true)) {
                continue;
            }

            if ($this->isSubclassOf($declaringClassName, $parentClassName, $parentClassByClass)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, string|null> $parentClassByClass
     */
    private function isSubclassOf(string $className, string $ancestorClassName, array $parentClassByClass): bool
    {
        $currentClassName = $parentClassByClass[$className] ?? null;

        while ($currentClassName !== null) {
            if ($currentClassName === $ancestorClassName) {
                return true;
            }

            $currentClassName = $parentClassByClass[$currentClassName] ?? null;
        }

        return false;
    }
}
