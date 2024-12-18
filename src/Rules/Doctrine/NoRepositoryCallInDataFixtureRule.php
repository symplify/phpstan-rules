<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \TomasVotruba\Handyman\Tests\PHPStan\Rule\NoRepositoryCallInDataFixtureRule\NoRepositoryCallInDataFixtureRuleTest
 *
 * @implements Rule<MethodCall>
 */
final class NoRepositoryCallInDataFixtureRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Refactor read-data fixtures to write-only, make use of references';

    /**
     * @var string
     */
    private const FIXTURE_INTERFACE = 'Doctrine\Common\DataFixtures\FixtureInterface';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->isFirstClassCallable()) {
            return [];
        }

        if (! $this->isDataFixtureClass($scope)) {
            return [];
        }

        if (! $node->name instanceof Identifier) {
            return [];
        }

        $methodName = $node->name->toString();
        if (! in_array($methodName, ['getRepository', 'find', 'findAll', 'findBy', 'findOneBy'])) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isDataFixtureClass(Scope $scope): bool
    {
        if (! $scope->isInClass()) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        return $classReflection->isSubclassOf(self::FIXTURE_INTERFACE);
    }
}
