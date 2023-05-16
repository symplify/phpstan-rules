<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoFuncCallInMethodCallRule\NoFuncCallInMethodCallRuleTest
 */
final class NoFuncCallInMethodCallRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Separate function "%s()" in method call to standalone row to improve readability';

    /**
     * @var string[]
     */
    private const ALLOWED_FUNC_CALL_NAMES = ['getcwd', 'sys_get_temp_dir'];

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $messages = [];

        foreach ($node->getArgs() as $arg) {
            if (! $arg->value instanceof FuncCall) {
                continue;
            }

            $funcCallName = $this->resolveFuncCallName($arg);
            if ($this->shouldSkipFuncCallName($funcCallName)) {
                continue;
            }

            if ($this->isSprintfInConsoleOutput($funcCallName, $scope, $node)) {
                continue;
            }

            $messages[] = sprintf(self::ERROR_MESSAGE, $funcCallName);
        }

        return $messages;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run($value): void
    {
        $this->someMethod(strlen('fooo'));
    }

    // ...
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run($value): void
    {
        $fooLength = strlen('fooo');
        $this->someMethod($fooLength);
    }

    // ...
}
CODE_SAMPLE
            ),
        ]);
    }

    private function resolveFuncCallName(Arg $arg): string
    {
        /** @var FuncCall $funcCall */
        $funcCall = $arg->value;
        if ($funcCall->name instanceof Expr) {
            return '*dynamic*';
        }

        return (string) $funcCall->name;
    }

    private function shouldSkipFuncCallName(string $funcCallName): bool
    {
        if (strpos($funcCallName, '\\') !== false) {
            return true;
        }

        return in_array($funcCallName, self::ALLOWED_FUNC_CALL_NAMES, true);
    }

    private function isSprintfInConsoleOutput(string $funcCallName, Scope $scope, MethodCall $methodCall): bool
    {
        if ($funcCallName !== 'sprintf') {
            return false;
        }

        $callerType = $scope->getType($methodCall->var);

        foreach ($callerType->getObjectClassReflections() as $objectClassReflection) {
            /** @var ClassReflection $objectClassReflection */
            if ($objectClassReflection->getName() === 'Symfony\Component\Console\Output\OutputInterface') {
                return true;
            }

            if ($objectClassReflection->isSubclassOf('Symfony\Component\Console\Output\OutputInterface')) {
                return true;
            }

            if ($objectClassReflection->isSubclassOf('Illuminate\Console\Command')) {
                return true;
            }
        }

        return false;
    }
}
