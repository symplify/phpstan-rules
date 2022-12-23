<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity\Rules;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\CognitiveComplexity\AstCognitiveComplexityAnalyzer;

/**
 * @deprecated
 */
final class FunctionLikeCognitiveComplexityRule implements Rule
{
    /**
     * @var \Symplify\PHPStanRules\CognitiveComplexity\AstCognitiveComplexityAnalyzer
     */
    private $astCognitiveComplexityAnalyzer;
    /**
     * @var int
     */
    private $maxMethodCognitiveComplexity = 8;
    public function __construct(AstCognitiveComplexityAnalyzer $astCognitiveComplexityAnalyzer, int $maxMethodCognitiveComplexity = 8)
    {
        $this->astCognitiveComplexityAnalyzer = $astCognitiveComplexityAnalyzer;
        $this->maxMethodCognitiveComplexity = $maxMethodCognitiveComplexity;
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return FunctionLike::class;
    }

    /**
     * @param FunctionLike $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        return [
            RuleErrorBuilder::message(sprintf(
                'The "%s" rule was deprecated and moved to "%s" package that has much simpler configuration. Use it instead.',
                self::class,
                'https://github.com/TomasVotruba/cognitive-complexity'
            ))->build(),
        ];
    }
}
