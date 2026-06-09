<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\Type;
use Symplify\PHPStanRules\Enum\RuleIdentifier\RectorRuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;
use Symplify\PHPStanRules\TypeAnalyzer\RectorAllowedAutoloadedTypeAnalyzer;

/**
 * @see https://github.com/rectorphp/rector/issues/5906
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Rector\NoInstanceOfStaticReflectionRule\NoInstanceOfStaticReflectionRuleTest
 *
 * @implements Rule<Expr>
 */
final class NoInstanceOfStaticReflectionRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of "instanceof/is_a()" use ReflectionProvider service or "(new ObjectType(<desired_type>))->isSuperTypeOf(<element_type>)" for static reflection to work';

    public function getNodeType(): string
    {
        return Expr::class;
    }

    /**
     * @param Expr $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node instanceof FuncCall && ! $node instanceof Instanceof_) {
            return [];
        }

        $exprStaticType = $this->resolveExprStaticType($node, $scope);
        if (! $exprStaticType instanceof Type) {
            return [];
        }

        if (RectorAllowedAutoloadedTypeAnalyzer::isAllowedType($exprStaticType)) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RectorRuleIdentifier::NO_INSTANCE_OF_STATIC_REFLECTION)
            ->build()];
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall|\PhpParser\Node\Expr\Instanceof_ $node
     */
    private function resolveExprStaticType($node, Scope $scope): ?Type
    {
        if ($node instanceof Instanceof_) {
            return $this->resolveInstanceOfType($node, $scope);
        }

        if (! NamingHelper::isName($node->name, 'is_a')) {
            return null;
        }

        $typeArgValue = $node->getArgs()[1]->value;
        return $scope->getType($typeArgValue);
    }

    private function resolveInstanceOfType(Instanceof_ $instanceof, Scope $scope): ?Type
    {
        if ($instanceof->class instanceof Name) {
            $className = $instanceof->class->toString();

            // skip self as allowed
            if ($className === 'self') {
                return null;
            }

            return new ConstantStringType($instanceof->class->toString());
        }

        return $scope->getType($instanceof->class);
    }
}
