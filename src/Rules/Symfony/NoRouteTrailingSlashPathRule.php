<?php

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyControllerAnalyzer;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\NoRouteTrailingSlashPathRule\NoRouteTrailingSlashPathRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final class NoRouteTrailingSlashPathRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid trailing slash in route path "%s", to prevent redirects and SEO issues';

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
        if ($node->isMagic() || ! $node->isPublic()) {
            return [];
        }

        if (! SymfonyControllerAnalyzer::isControllerScope($scope)) {
            return [];
        }

        $routePath = $this->matchRouteDocblockPath($node);
        if (! is_string($routePath)) {
            return [];
        }

        // path is valid
        if ($routePath === '/' || substr_compare($routePath, '/', -strlen('/')) !== 0) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $routePath))
            ->identifier(SymfonyRuleIdentifier::NO_ROUTE_TRAILING_SLASH_PATH)
            ->build();

        return [$identifierRuleError];
    }

    private function matchRouteDocblockPath(ClassMethod $classMethod): ?string
    {
        $docComment = $classMethod->getDocComment();
        if (! $docComment instanceof Doc) {
            return null;
        }

        // not a route
        if (strpos($docComment->getText(), 'Route') === false) {
            return null;
        }

        /** @see https://regex101.com/r/Qo7aLu/1 */
        preg_match('#@Route\((path=)?"(?<path>[\/\w\-]+)"#', $docComment->getText(), $matches);

        return $matches['path'] ?? null;
    }
}
