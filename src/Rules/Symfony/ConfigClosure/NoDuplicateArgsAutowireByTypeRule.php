<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony\ConfigClosure;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Enum\SymfonyFunctionName;
use Symplify\PHPStanRules\Helper\NamingHelper;
use Symplify\PHPStanRules\Symfony\Reflection\ClassConstructorTypesResolver;

/**
 * Spot pointless explicit services:
 *
 * $services->set(SomeService::class)
 *     ->args([ref(ExactType::class)]);
 *
 * class SomeService {
 *     public function __construct(private ExactType $exactType)
 * }
 *
 * @implements Rule<MethodCall>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoDuplicateArgsAutowireByTypeRule\NoDuplicateArgsAutowireByTypeRuleTest
 */
final class NoDuplicateArgsAutowireByTypeRule implements Rule
{
    /**
     * @readonly
     */
    private ClassConstructorTypesResolver $classConstructorTypesResolver;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of passing "%s" to args(), remove the line and let autowiring handle it';

    public function __construct(ClassConstructorTypesResolver $classConstructorTypesResolver)
    {
        $this->classConstructorTypesResolver = $classConstructorTypesResolver;
    }

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return IdentifierRuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! NamingHelper::isName($node->name, 'args')) {
            return [];
        }

        if (count($node->getArgs()) !== 1) {
            return [];
        }

        $firstArg = $node->getArgs()[0];
        if (! $firstArg->value instanceof Array_) {
            return [];
        }

        $classArgumentNamesToTypes = $this->classConstructorTypesResolver->resolveClassConstructorNamesToTypes($node);
        if ($classArgumentNamesToTypes === []) {
            return [];
        }

        $ruleErrors = [];

        $array = $firstArg->value;
        foreach ($array->items as $arrayItem) {
            if (! $arrayItem->value instanceof FuncCall) {
                continue;
            }

            $funcCall = $arrayItem->value;
            if (! NamingHelper::isNames($funcCall->name, [SymfonyFunctionName::REF, SymfonyFunctionName::SERVICE])) {
                continue;
            }

            $referenceFuncCall = $arrayItem->value;
            $referenceExpr = $referenceFuncCall->getArgs()[0]->value;

            if (! $referenceExpr instanceof ClassConstFetch) {
                continue;
            }

            $referenceServiceType = NamingHelper::getName($referenceExpr->class);
            if (! in_array($referenceServiceType, $classArgumentNamesToTypes)) {
                continue;
            }

            $ruleErrors[] = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $referenceServiceType))
                ->identifier(SymfonyRuleIdentifier::NO_DUPLICATE_ARGS_AUTOWIRE_BY_TYPE)
                ->line($referenceFuncCall->getStartLine())
                ->build();
        }

        return $ruleErrors;
    }
}
