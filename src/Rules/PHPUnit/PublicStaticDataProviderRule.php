<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\PHPUnit;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\PHPUnitRuleIdentifier;
use Symplify\PHPStanRules\PHPUnit\DataProviderMethodResolver;
use Symplify\PHPStanRules\Testing\PHPUnitTestAnalyser;

/**
 * PHPUnit data provider have to be public and static
 *
 * @implements Rule<InClassNode>
 */
final class PublicStaticDataProviderRule implements Rule
{
    /**
     * @api used in test
     * @var string
     */
    public const PUBLIC_ERROR_MESSAGE = 'PHPUnit data provider method "%s" must be public';

    /**
     * @api used in test
     * @var string
     */
    public const STATIC_ERROR_MESSAGE = 'PHPUnit data provider method "%s" must be static';

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! PHPUnitTestAnalyser::isTestClass($scope)) {
            return [];
        }

        $ruleErrors = [];

        $classLike = $node->getOriginalNode();
        foreach ($classLike->getMethods() as $classMethod) {
            if (! PHPUnitTestAnalyser::isTestClassMethod($classMethod)) {
                continue;
            }

            $dataProviderMethodName = DataProviderMethodResolver::match($classMethod);
            if (! is_string($dataProviderMethodName)) {
                continue;
            }

            $dataProviderClassMethod = $classLike->getMethod($dataProviderMethodName);
            if (! $dataProviderClassMethod instanceof ClassMethod) {
                continue;
            }

            if (! $dataProviderClassMethod->isStatic()) {
                $errorMessage = sprintf(self::STATIC_ERROR_MESSAGE, $dataProviderMethodName);
                $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                    ->identifier('phpunit.staticDataProvider')
                    ->line($dataProviderClassMethod->getLine())
                    ->build();
            }

            if (! $dataProviderClassMethod->isPublic()) {
                $errorMessage = sprintf(self::PUBLIC_ERROR_MESSAGE, $dataProviderMethodName);
                $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                    ->identifier(PHPUnitRuleIdentifier::PUBLIC_STATIC_DATA_PROVIDER)
                    ->line($dataProviderClassMethod->getLine())
                    ->build();
            }
        }

        return $ruleErrors;
    }
}
