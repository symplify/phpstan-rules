<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony\ConfigClosure;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
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
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoDuplicateArgAutowireByTypeRule\NoDuplicateArgAutowireByTypeRuleTest
 */
final class NoDuplicateArgAutowireByTypeRule implements Rule
{
    /**
     * @readonly
     */
    private ClassConstructorTypesResolver $classConstructorTypesResolver;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of passing "%s" to arg(), remove the line and let autowiring handle it';

    /**
     * @todo possible extract to an own rule, include more common Symfony services
     *
     * @var string[]
     */
    private const NAMED_AUTOWIRED_TYPES = ['request_stack'];

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
        if (! NamingHelper::isName($node->name, 'arg')) {
            return [];
        }

        if (count($node->getArgs()) !== 2) {
            return [];
        }

        $firstArg = $node->getArgs()[0];
        if (! $firstArg->value instanceof String_) {
            return [];
        }

        $secondArg = $node->getArgs()[1];
        if (! $secondArg->value instanceof FuncCall) {
            return [];
        }

        $referenceFuncCall = $secondArg->value;
        if (! NamingHelper::isNames($referenceFuncCall->name, [SymfonyFunctionName::SERVICE, SymfonyFunctionName::REF])) {
            return [];
        }

        $currentArgumentName = ltrim($firstArg->value->value, '$');

        // 1. compare referenced type and constructor type
        $classArgumentNamesToTypes = $this->classConstructorTypesResolver->resolveClassConstructorNamesToTypes($node);
        $referenceExpr = $referenceFuncCall->getArgs()[0]->value;

        if (isset($classArgumentNamesToTypes[$currentArgumentName])) {
            $constructorType = $classArgumentNamesToTypes[$currentArgumentName];

            if ($referenceExpr instanceof ClassConstFetch) {
                $referenceServiceType = NamingHelper::getName($referenceExpr->class);
                if ($referenceServiceType === $constructorType) {
                    $ruleError = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $constructorType))
                        ->identifier(SymfonyRuleIdentifier::NO_DUPLICATE_ARG_AUTOWIRE_BY_TYPE)
                        ->line($referenceFuncCall->getStartLine())
                        ->build();

                    return [$ruleError];
                }
            }
        }

        // 2. special case for string known values
        if ($referenceExpr instanceof String_ && in_array($referenceExpr->value, self::NAMED_AUTOWIRED_TYPES)) {
            $ruleError = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $referenceExpr->value))
                ->identifier(SymfonyRuleIdentifier::NO_DUPLICATE_ARG_AUTOWIRE_BY_TYPE)
                ->line($referenceFuncCall->getStartLine())
                ->build();

            return [$ruleError];
        }

        return [];
    }
}
