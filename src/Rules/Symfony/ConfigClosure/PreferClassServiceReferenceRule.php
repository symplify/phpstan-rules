<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony\ConfigClosure;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Collector\ClassTargetServiceAliasCollector;
use Symplify\PHPStanRules\Collector\ServiceStringReferenceCollector;

/**
 * Reports a config-closure service() reference that asks for a service by a string id an alias already points at
 * the very class of, e.g. service('some.helper') while $services->alias('some.helper', SomeHelper::class) is
 * registered.
 *
 * The class name names the very same service, so the reference should say the same by the type instead:
 *
 *     $services->alias('some.helper', SomeHelper::class);
 *     ...
 *     ->args([service('some.helper')]);
 *
 *     ->args([service(SomeHelper::class)]);
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\PreferClassServiceReferenceRule\PreferClassServiceReferenceRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final class PreferClassServiceReferenceRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Reference the service by its class, service(%s::class), rather than by the string id "%s" a class name alias already covers';

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $node
     *
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classNamesByServiceId = $this->resolveClassNamesByServiceId($node);

        /** @var array<string, list<array{string, int}>> $referencesByFilePath */
        $referencesByFilePath = $node->get(ServiceStringReferenceCollector::class);

        $ruleErrors = [];

        foreach ($referencesByFilePath as $filePath => $references) {
            foreach ($references as [$serviceId, $line]) {
                $className = $classNamesByServiceId[$serviceId] ?? null;
                if ($className === null) {
                    continue;
                }

                $ruleErrors[] = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $className, $serviceId))
                    ->identifier('symfony.preferClassServiceReference')
                    ->file($filePath)
                    ->line($line)
                    ->build();
            }
        }

        return $ruleErrors;
    }

    /**
     * @return array<string, string> the class name every string service id is aliased to
     */
    private function resolveClassNamesByServiceId(CollectedDataNode $collectedDataNode): array
    {
        /** @var array<string, list<array{string, string, int}>> $aliasesByFilePath */
        $aliasesByFilePath = $collectedDataNode->get(ClassTargetServiceAliasCollector::class);

        $classNamesByServiceId = [];

        foreach ($aliasesByFilePath as $aliases) {
            foreach ($aliases as [$serviceId, $className]) {
                $classNamesByServiceId[$serviceId] = $className;
            }
        }

        return $classNamesByServiceId;
    }
}
