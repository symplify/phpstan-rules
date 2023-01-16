<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\PropertyProperty;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see https://github.com/object-calisthenics/phpcs-calisthenics-rules#6-do-not-abbreviate
 *
 * @see \Symplify\PHPStanRules\Tests\ObjectCalisthenics\Rules\NoShortNameRule\NoShortNameRuleTest
 */
final class NoShortNameRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not name "%s", shorter than %d chars';

    /**
     * @param string[] $allowedShortNames
     */
    public function __construct(
        private readonly int $minNameLength,
        private readonly array $allowedShortNames = ['i', 'j', 'y', 'z']
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [
            ClassLike::class,
            Function_::class,
            ClassMethod::class,
            Const_::class,
            PropertyProperty::class,
            Variable::class,
            Param::class,
        ];
    }

    /**
     * @param ClassLike|Function_|ClassMethod|Const_|PropertyProperty|Variable|Param $node
     * @return array<int, string>
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node instanceof Variable || $node instanceof Param) {
            if ($node instanceof Param) {
                $node = $node->var;
            }

            if (! $node instanceof Variable) {
                return [];
            }

            return $this->processVariable($node);
        }

        $name = (string) $node->name;
        if ($this->isNameValid($name)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $name, $this->minNameLength);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
function is()
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
function isClass()
{
}
CODE_SAMPLE
                ,
                [
                    'minNameLength' => 3,
                ]
            ),
        ]);
    }

    /**
     * @return string[]
     */
    private function processVariable(Variable $variable): array
    {
        if (! is_string($variable->name)) {
            return [];
        }

        $variableName = $variable->name;
        if ($this->isNameValid($variableName)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $variableName, $this->minNameLength);
        return [$errorMessage];
    }

    private function isNameValid(string $name): bool
    {
        if (Strings::length($name) >= $this->minNameLength) {
            return true;
        }

        return in_array($name, $this->allowedShortNames, true);
    }
}
