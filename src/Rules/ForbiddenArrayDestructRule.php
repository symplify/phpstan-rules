<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayDestructRule\ForbiddenArrayDestructRuleTest
 */
final class ForbiddenArrayDestructRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Array destruct is not allowed. Use value object to pass data instead';

    /**
     * @var string
     * @see https://regex101.com/r/dhGhYp/1
     */
    private const VENDOR_DIRECTORY_REGEX = '#/vendor/#';

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Assign::class;
    }

    /**
     * @param Assign $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->var instanceof Array_) {
            return [];
        }

        // swaps are allowed
        if ($node->expr instanceof Array_) {
            return [];
        }

        if ($this->isAllowedCall($node)) {
            return [];
        }

        // is 3rd party method call → nothing we can do about it
        if ($this->isVendorProvider($node, $scope)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run(): void
    {
        [$firstValue, $secondValue] = $this->getRandomData();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run(): void
    {
        $valueObject = $this->getValueObject();
        $firstValue = $valueObject->getFirstValue();
        $secondValue = $valueObject->getSecondValue();
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isAllowedCall(Assign $assign): bool
    {
        // "explode()" is allowed
        if ($assign->expr instanceof FuncCall && $assign->expr->name instanceof Name && $assign->expr->name->toString() === 'explode') {
            return true;
        }

        // Strings::split() is allowed
        if (! $assign->expr instanceof StaticCall) {
            return false;
        }

        $staticCall = $assign->expr;
        if (! $staticCall->name instanceof Identifier) {
            return false;
        }

        return $staticCall->name->toString() === 'split';
    }

    private function isVendorProvider(Assign $assign, Scope $scope): bool
    {
        if (! $assign->expr instanceof MethodCall) {
            return false;
        }

        $callerType = $scope->getType($assign->expr->var);
        if (! $callerType instanceof ObjectType) {
            return false;
        }

        $classReflection = $this->reflectionProvider->getClass($callerType->getClassName());
        $fileName = $classReflection->getFileName();
        if ($fileName === null) {
            return true;
        }

        return (bool) Strings::match($fileName, self::VENDOR_DIRECTORY_REGEX);
    }
}
