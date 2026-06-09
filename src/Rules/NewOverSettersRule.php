<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Collector\NewWithFollowingSettersCollector;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @see NewWithFollowingSettersCollector
 * @see \Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\NewOverSettersRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final class NewOverSettersRule implements Rule
{
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;
    /**
     * @readonly
     */
    private bool $isEnabled;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" is always created with same %d setter(s): "%s()"%sPass these values via constructor instead';

    public function __construct(ReflectionProvider $reflectionProvider, bool $isEnabled)
    {
        $this->reflectionProvider = $reflectionProvider;
        $this->isEnabled = $isEnabled;
    }

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $node
     * @return IdentifierRuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // enable with "ctor: true" parameter
        if (! $this->isEnabled) {
            return [];
        }

        $collectedDataByFile = $node->get(NewWithFollowingSettersCollector::class);

        // group class + always called setters
        // if 0 setters, skipp it
        // if its always the same setters, report it

        $classesToSetterHashes = $this->groupClassesToSetterNames($collectedDataByFile);

        $ruleErrors = [];

        foreach ($classesToSetterHashes as $className => $setterNameGroups) {
            // we need at least 2 different occurrences to compare
            if (count($setterNameGroups) === 1) {
                continue;
            }

            if (! $this->areAlwaysTheSameMethodNames($setterNameGroups)) {
                continue;
            }

            if (! $this->reflectionProvider->hasClass($className)) {
                continue;
            }

            $classReflection = $this->reflectionProvider->getClass($className);
            $setterNameGroup = $setterNameGroups[0];

            $errorMessage = sprintf(
                self::ERROR_MESSAGE,
                $className,
                count($setterNameGroup),
                implode('()", "', $setterNameGroup),
                PHP_EOL
            );

            $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                ->identifier(RuleIdentifier::NEW_OVER_SETTERS)
                ->file((string) $classReflection->getFileName())
                ->build();
        }

        return $ruleErrors;
    }

    /**
     * @param array<string, list<array<array{variableName: string, className: string, setterNames: string[]}>>> $collectedDataByFile
     * @return array<string, string[][]>
     */
    private function groupClassesToSetterNames(array $collectedDataByFile): array
    {
        $classesToSetters = [];
        foreach ($collectedDataByFile as $collectedData) {

            foreach ($collectedData as $collectedItems) {
                foreach ($collectedItems as $collectedItem) {
                    if (count($collectedItem[NewWithFollowingSettersCollector::SETTER_NAMES]) === 0) {
                        continue;
                    }

                    $className = $collectedItem['className'];

                    $uniqueSetterNames = array_unique($collectedItem[NewWithFollowingSettersCollector::SETTER_NAMES]);
                    sort($uniqueSetterNames);

                    $classesToSetters[$className][] = $uniqueSetterNames;
                }
            }
        }

        return $classesToSetters;
    }

    /**
     * @param array<string[]> $methodNameGroups
     */
    private function areAlwaysTheSameMethodNames(array $methodNameGroups): bool
    {
        $methodNameHashes = [];
        foreach ($methodNameGroups as $methodNameGroup) {
            $methodNameHashes[] = implode('_', $methodNameGroup);
        }

        return count(array_unique($methodNameHashes)) === 1;
    }
}
