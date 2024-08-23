<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\PHPUnit;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @implements Rule<MethodCall>
 */
final class NoTestMocksRule implements Rule
{
    /**
     * @var string[]
     * @readonly
     */
    private $allowedTypes = [];
    /**
     * @api
     * @var string
     */
    public const ERROR_MESSAGE = 'Mocking "%s" class is forbidden. Use direct/anonymous class instead for better static analysis';

    /**
     * @var string[]
     */
    private const MOCKING_METHOD_NAMES = ['createMock', 'createPartialMock', 'createConfiguredMock', 'createStub'];

    /**
     * @param string[] $allowedTypes
     */
    public function __construct(array $allowedTypes = [])
    {
        $this->allowedTypes = $allowedTypes;
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier) {
            return [];
        }

        $methodName = $node->name->toString();
        if (! in_array($methodName, self::MOCKING_METHOD_NAMES, true)) {
            return [];
        }

        $mockedObjectType = $this->resolveMockedObjectType($node, $scope);
        if (! $mockedObjectType instanceof ObjectType) {
            return [];
        }

        if ($this->isAllowedType($mockedObjectType)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $mockedObjectType->getClassName());

        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

final class SkipApiMock extends TestCase
{
    public function test()
    {
        $someTypeMock = $this->createMock(SomeType::class);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

final class SkipApiMock extends TestCase
{
    public function test()
    {
        $someTypeMock = new class() implements SomeType {};
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function resolveMockedObjectType(MethodCall $methodCall, Scope $scope): ?ObjectType
    {
        $args = $methodCall->getArgs();

        $mockedArgValue = $args[0]->value;
        $variableType = $scope->getType($mockedArgValue);

        foreach ($variableType->getConstantStrings() as $constantStringType) {
            return new ObjectType($constantStringType->getValue());
        }

        return null;
    }

    private function isAllowedType(ObjectType $objectType): bool
    {
        foreach ($this->allowedTypes as $allowedType) {
            if ($objectType->getClassName() === $allowedType) {
                return true;
            }

            if ($objectType->isInstanceOf($allowedType)->yes()) {
                return true;
            }
        }

        return false;
    }
}
