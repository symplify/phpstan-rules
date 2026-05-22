<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony\ConfigClosure;

use PhpParser\Node;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyClosureDetector;

/**
 * @implements Rule<Closure>
 */
final class ServicesExcludedDirectoryMustExistRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Services excluded path "%s" does not exists. You can remove it';

    public function getNodeType(): string
    {
        return Closure::class;
    }

    /**
     * @param Closure $node
     * @return IdentifierRuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! SymfonyClosureDetector::detect($node)) {
            return [];
        }

        $excludeMethodCalls = $this->resolveExcludeMethodCalls($node);
        if ($excludeMethodCalls === []) {
            return [];
        }

        $ruleErrors = [];

        foreach ($excludeMethodCalls as $excludeMethodCall) {
            // check all array args
            $firstArgValue = $excludeMethodCall->getArgs()[0]->value;
            if (! $firstArgValue instanceof Array_) {
                continue;
            }

            foreach ($firstArgValue->items as $arrayItem) {
                $directoryPath = $this->resolveDirectoryPath($arrayItem, $scope);
                if (! is_string($directoryPath)) {
                    continue;
                }

                // path exists, all good
                if (file_exists($directoryPath)) {
                    continue;
                }

                /** @var BinaryOp $binarOp */
                $binarOp = $arrayItem->value;

                /** @var String_ $pathValue */
                $pathValue = $binarOp->right;

                $errorMessage = sprintf(self::ERROR_MESSAGE, $pathValue->value);

                $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                    ->line($arrayItem->getStartLine())
                    ->identifier(SymfonyRuleIdentifier::SERVICES_EXCLUDED_DIRECTORY_MUST_EXIST)
                    ->build();
            }
        }

        return $ruleErrors;
    }

    private function resolveDirectoryPath(ArrayItem $arrayItem, Scope $scope): ?string
    {
        if (! $arrayItem->value instanceof Concat) {
            return null;
        }

        $concat = $arrayItem->value;
        if (! $concat->left instanceof Dir) {
            return null;
        }

        if (! $concat->right instanceof String_) {
            return null;
        }

        $stringPart = $concat->right->value;

        // uses magic mask, nothing to validate
        if (strpos($stringPart, '*') !== false || strpos($stringPart, '{') !== false) {
            return null;
        }

        // check full path
        $directory = dirname($scope->getFile());
        return $directory . '/' . $stringPart;
    }

    /**
     * @return array<MethodCall>
     */
    private function resolveExcludeMethodCalls(Closure $closure): array
    {
        $nodeFinder = new NodeFinder();

        $methodCalls = $nodeFinder->find($closure, function (Node $node): bool {
            if (! $node instanceof MethodCall) {
                return false;
            }

            if ($node->isFirstClassCallable()) {
                return false;
            }

            // must have exactly 1 arg
            if (count($node->getArgs()) !== 1) {
                return false;
            }

            return NamingHelper::isName($node->name, 'exclude');
        });

        /** @var MethodCall[] $methodCalls */
        return $methodCalls;
    }
}
