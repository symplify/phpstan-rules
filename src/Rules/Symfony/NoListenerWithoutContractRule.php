<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Based on https://tomasvotruba.com/blog/2019/07/22/how-to-convert-listeners-to-subscribers-and-reduce-your-configs
 * Subscribers have much better PHP support - IDE, PHPStan + Rector - than simple yaml files
 *
 * @implements Rule<InClassNode>
 */
final class NoListenerWithoutContractRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'There should be no listeners defined in yaml config, use contract + PHP instead';

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $scope->isInClass()) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! str_ends_with($classReflection->getName(), 'Listener')) {
            return [];
        }

        $class = $node->getOriginalNode();
        if (! $class instanceof Class_) {
            return [];
        }

        if ($class->implements !== []) {
            return [];
        }

        $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)->build();

        return [$ruleError];
    }
}
