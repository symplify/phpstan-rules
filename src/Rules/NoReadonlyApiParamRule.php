<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * A promoted constructor property marked "@api" in its @param docblock is part of the public API and
 * may be swapped in tests via reflection, so it must not be readonly.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\NoReadonlyApiParamRule\NoReadonlyApiParamRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final class NoReadonlyApiParamRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Parameter "$%s" is marked "@api" and may be swapped via reflection in tests, so it must not be readonly. Remove the readonly modifier';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return [];
        }

        $docText = $docComment->getText();

        $ruleErrors = [];
        foreach ($node->params as $param) {
            if (! $param->isReadonly()) {
                continue;
            }

            if (! $param->var instanceof Variable || ! is_string($param->var->name)) {
                continue;
            }

            $paramName = $param->var->name;
            if (! $this->hasApiParamAnnotation($docText, $paramName)) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $paramName);

            $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                ->identifier('symplify.noReadonlyApiParam')
                ->line($param->getStartLine())
                ->build();
        }

        return $ruleErrors;
    }

    private function hasApiParamAnnotation(string $docText, string $paramName): bool
    {
        foreach (explode("\n", $docText) as $line) {
            if (strpos($line, '@param') === false) {
                continue;
            }

            if (strpos($line, '$' . $paramName) === false) {
                continue;
            }

            if (strpos($line, '@api') !== false) {
                return true;
            }
        }

        return false;
    }
}
