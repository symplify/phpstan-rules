<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Entropy\Validation\Assert;
use PhpParser\Node;
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Scalar\EncapsedStringPart;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Node>
 */
final class ForbiddenNodeRule implements Rule
{
    /**
     * @var array<class-string<Node>>
     * @readonly
     */
    private array $forbiddenNodes;

    /**
     * @var string
     */
    public const ERROR_MESSAGE = '"%s" is forbidden to use';

    /**
     * @readonly
     */
    private Standard $standard;

    /**
     * @param array<class-string<Node>> $forbiddenNodes
     */
    public function __construct(
        array $forbiddenNodes
    ) {
        $this->forbiddenNodes = $forbiddenNodes;
        Assert::allIsAOf($forbiddenNodes, Node::class);

        $this->standard = new Standard();
    }

    public function getNodeType(): string
    {
        return Node::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        foreach ($this->forbiddenNodes as $forbiddenNode) {
            if (! $node instanceof $forbiddenNode) {
                continue;
            }

            // this node can't be printed as standalone
            if ($node instanceof EncapsedStringPart) {
                $contents = $this->standard->prettyPrintExpr(new Encapsed([$node]));
            } else {
                $contents = $this->standard->prettyPrint([$node]);
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $contents);

            $ruleError = RuleErrorBuilder::message($errorMessage)
                ->identifier('symplify.forbiddenNode')
                ->build();

            return [$ruleError];
        }

        return [];
    }
}
