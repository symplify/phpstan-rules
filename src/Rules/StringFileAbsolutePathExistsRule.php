<?php

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<Concat>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\StringFileAbsolutePathExistsRule\StringFileAbsolutePathExistsRuleTest
 */
final class StringFileAbsolutePathExistsRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'File "%s" could not be found. Make sure it exists';

    /**
     * @var string[]
     */
    private const SUFFIXES_TO_CHECK = [
        '.sql',
        '.php',
        '.yml',
        '.yaml',
        '.json',
    ];

    public function getNodeType(): string
    {
        return Concat::class;
    }

    /**
     * @param Concat $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // look for __DIR__ . '/some_file.<suffix>'
        if (! $node->left instanceof Dir) {
            return [];
        }

        if (! $node->right instanceof String_) {
            return [];
        }

        $stringValue = $node->right->value;
        if (! $this->isDesiredFileSuffix($stringValue)) {
            return [];
        }

        // probably glob or wildcard, cannot be checked
        if (strpos($stringValue, '*') !== false) {
            return [];
        }

        $absoluteFilePath = $this->getAbsoluteFilePath($scope, $stringValue);
        if (file_exists($absoluteFilePath)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $absoluteFilePath);

        $identifierRuleError = RuleErrorBuilder::message($errorMessage)
            ->identifier(RuleIdentifier::STRING_FILE_ABSOLUTE_PATH_EXISTS)
            ->build();

        return [$identifierRuleError];
    }

    private function getAbsoluteFilePath(Scope $scope, string $stringValue): string
    {
        $directorPath = dirname($scope->getFile());
        return $directorPath . $stringValue;
    }

    private function isDesiredFileSuffix(string $stringValue): bool
    {
        foreach (self::SUFFIXES_TO_CHECK as $suffixToCheck) {
            if (substr_compare($stringValue, $suffixToCheck, -strlen($suffixToCheck)) === 0) {
                return true;
            }
        }

        return false;
    }
}
