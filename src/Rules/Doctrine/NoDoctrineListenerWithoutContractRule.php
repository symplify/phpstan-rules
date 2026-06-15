<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Doctrine\DoctrineEventSubscriberAnalyzer;
use Symplify\PHPStanRules\Enum\RuleIdentifier\DoctrineRuleIdentifier;

/**
 * Based on https://tomasvotruba.com/blog/2019/07/22/how-to-convert-listeners-to-subscribers-and-reduce-your-configs
 * Subscribers have much better PHP support - IDE, PHPStan + Rector - than simple yaml files
 *
 * @implements Rule<InClassNode>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Doctrine\NoDoctrineListenerWithoutContractRule\NoDoctrineListenerWithoutContractRuleTest
 */
final class NoDoctrineListenerWithoutContractRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'There should be no Doctrine listeners modified in config. Implement  "Document\Event\EventSubscriber" to provide events in the class itself';

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

        if (! DoctrineEventSubscriberAnalyzer::detect($classLike)) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(DoctrineRuleIdentifier::NO_LISTENER_WITHOUT_CONTRACT)
            ->build();

        return [$identifierRuleError];
    }
}
