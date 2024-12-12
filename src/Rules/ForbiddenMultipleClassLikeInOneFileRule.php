<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @implements Rule<FileNode>
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenMultipleClassLikeInOneFileRule\ForbiddenMultipleClassLikeInOneFileRuleTest
 */
final class ForbiddenMultipleClassLikeInOneFileRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Multiple class/interface/trait is not allowed in single file';

    /**
     * @readonly
     */
    private NodeFinder $nodeFinder;

    public function __construct(
    ) {
        $this->nodeFinder = new NodeFinder();
    }

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    /**
     * @param FileNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        /** @var ClassLike[] $classLikes */
        $classLikes = $this->nodeFinder->findInstanceOf($node->getNodes(), ClassLike::class);

        $findclassLikes = [];
        foreach ($classLikes as $classLike) {
            if (! $classLike->name instanceof Identifier) {
                continue;
            }

            $findclassLikes[] = $classLike;
        }

        if (count($findclassLikes) <= 1) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)->build()];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
// src/SomeClass.php
class SomeClass
{
}

interface SomeInterface
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
// src/SomeClass.php
class SomeClass
{
}

// src/SomeInterface.php
interface SomeInterface
{
}
CODE_SAMPLE
            ),
        ]);
    }
}
