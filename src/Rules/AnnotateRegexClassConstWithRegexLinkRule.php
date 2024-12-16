<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<ClassConst>
 * @see \Symplify\PHPStanRules\Tests\Rules\AnnotateRegexClassConstWithRegexLinkRule\AnnotateRegexClassConstWithRegexLinkRuleTest
 */
final class AnnotateRegexClassConstWithRegexLinkRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Add regex101.com link to that shows the regex in practise, so it will be easier to maintain in case of bug/extension in the future';

    /**
     * @var string
     * @see https://www.php.net/manual/en/reference.pcre.pattern.modifiers.php
     */
    private const ALL_MODIFIERS = 'imsxeADSUXJu';

    public function getNodeType(): string
    {
        return ClassConst::class;
    }

    /**
     * @param ClassConst $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (count($node->consts) !== 1) {
            return [];
        }

        $onlyConst = $node->consts[0];
        if (! $onlyConst->value instanceof String_) {
            return [];
        }

        $constantName = (string) $onlyConst->name;
        if (! $this->isRegexPatternConstantName($constantName)) {
            return [];
        }

        $stringValue = $onlyConst->value->value;
        if (! $this->isNonSingleCharRegexPattern($stringValue)) {
            return [];
        }

        // is regex patern
        if ($this->hasDocBlockWithRegexLink($node)) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::REGEX_ANNOTATE_CLASS_CONST)
            ->build()];
    }

    private function isNonSingleCharRegexPattern(string $value): bool
    {
        // skip 1-char regexs
        if (strlen($value) < 4) {
            return false;
        }

        $firstChar = $value[0];

        if (ctype_alpha($firstChar)) {
            return false;
        }

        $patternWithoutModifiers = rtrim($value, self::ALL_MODIFIERS);

        if (strlen($patternWithoutModifiers) < 1) {
            return false;
        }

        $lastChar = substr($patternWithoutModifiers, -1, 1);

        // this is probably a regex
        return $firstChar === $lastChar;
    }

    private function hasDocBlockWithRegexLink(ClassConst $classConst): bool
    {
        $docComment = $classConst->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        $docCommentText = $docComment->getText();
        return strpos($docCommentText, '@see https://regex101.com/r') !== false;
    }

    private function isRegexPatternConstantName(string $constantName): bool
    {
        return substr_compare($constantName, '_REGEX', -strlen('_REGEX')) === 0;
    }
}
