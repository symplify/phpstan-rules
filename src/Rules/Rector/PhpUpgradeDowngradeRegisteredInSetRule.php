<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Rector;

use Nette\Utils\Strings;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Rector\Set\ValueObject\DowngradeSetList;
use Rector\Set\ValueObject\SetList;
use SplFileInfo;
use Symplify\PHPStanRules\Enum\ClassName;
use Symplify\PHPStanRules\Enum\RectorRuleIdentifier;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\PHPStanRules\FileSystem\FileSystem;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Rector\PhpUpgradeDowngradeRegisteredInSetRule\PhpUpgradeDowngradeRegisteredInSetRuleTest
 *
 * @implements Rule<InClassNode>
 */
final class PhpUpgradeDowngradeRegisteredInSetRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Register "%s" service to "%s" config set';

    /**
     * @var string
     * @see https://regex101.com/r/VGmFKR/1
     */
    private const DOWNGRADE_PREFIX_REGEX = '#(?<is_downgrade>Downgrade)?Php(?<version>\d+)#';

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $className = $this->matchRectorClassName($scope);
        if ($className === null) {
            return [];
        }

        $configFilePath = $this->resolveRelatedConfigFilePath($className);
        if ($configFilePath === null) {
            return [];
        }

        $configContent = FileSystem::read($configFilePath);

        // is rule registered?
        if (strpos($configContent, $className) !== false) {
            return [];
        }

        $errorMessage = $this->createErrorMessage($configFilePath, $className);
        return [RuleErrorBuilder::message($errorMessage)
            ->identifier(RectorRuleIdentifier::UPGRADE_DOWNGRADE_REGISTERED_IN_SET)
            ->build()];
    }

    private function resolveRelatedConfigFilePath(string $className): ?string
    {
        $match = Strings::match($className, self::DOWNGRADE_PREFIX_REGEX);
        if ($match === null) {
            return null;
        }

        $constantName = 'PHP_' . $match['version'];
        if ($match['is_downgrade']) {
            $resolvedValue = constant(DowngradeSetList::class . '::' . $constantName);
            if (! is_string($resolvedValue)) {
                throw new ShouldNotHappenException();
            }

            return $resolvedValue;
        }

        $resolvedValue = constant(SetList::class . '::' . $constantName);
        if (! is_string($resolvedValue)) {
            throw new ShouldNotHappenException();
        }

        return $resolvedValue;
    }

    private function createErrorMessage(string $configFilePath, string $rectorClass): string
    {
        $configFileInfo = new SplFileInfo($configFilePath);
        $configFilename = $configFileInfo->getFilename();

        return sprintf(self::ERROR_MESSAGE, $rectorClass, $configFilename);
    }

    private function matchRectorClassName(Scope $scope): ?string
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        if (! $classReflection->is(ClassName::RECTOR)) {
            return null;
        }

        // configurable Rector can be registered optionally
        if ($classReflection->is(ClassName::CONFIGURABLE_RECTOR)) {
            return null;
        }

        return $classReflection->getName();
    }
}
