<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Symplify\PHPStanRules\Exception\DeprecatedException;

/**
 * @see \Symplify\PHPStanRules\Tests\CognitiveComplexity\AstCognitiveComplexityAnalyzer\AstCognitiveComplexityAnalyzerTest
 *
 * implements the concept described in https://www.sonarsource.com/resources/white-papers/cognitive-complexity/
 *
 * @deprecated
 */
final class AstCognitiveComplexityAnalyzer
{
    /**
     * @return never
     */
    public function analyzeClassLike(Class_ $class)
    {
        $deprecatedMessage = sprintf(
            'The "%s" service was deprecated and moved to "%s" package that has much simpler configuration. Use it instead.',
            self::class,
            'https://github.com/TomasVotruba/cognitive-complexity'
        );
        throw new DeprecatedException($deprecatedMessage);
    }

    /**
     * @api
     * @return never
     * @param \PhpParser\Node\Stmt\Function_|\PhpParser\Node\Stmt\ClassMethod $functionLike
     */
    public function analyzeFunctionLike($functionLike)
    {
        $deprecatedMessage = sprintf(
            'The "%s" service was deprecated and moved to "%s" package that has much simpler configuration. Use it instead.',
            self::class,
            'https://github.com/TomasVotruba/cognitive-complexity'
        );
        throw new DeprecatedException($deprecatedMessage);
    }
}
