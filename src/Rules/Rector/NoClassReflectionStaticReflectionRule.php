<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use ReflectionClass;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Symplify\PHPStanRules\TypeAnalyzer\RectorAllowedAutoloadedTypeAnalyzer;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Rector\NoClassReflectionStaticReflectionRule\NoClassReflectionStaticReflectionRuleTest
 *
 * @implements Rule<New_>
 */
final class NoClassReflectionStaticReflectionRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of "new ClassReflection()" use ReflectionProvider service or "(new PHPStan\Reflection\ClassReflection(<desired_type>))" for static reflection to work';

    public function getNodeType(): string
    {
        return New_::class;
    }

    /**
     * @param New_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (count($node->getArgs()) !== 1) {
            return [];
        }

        if (! $node->class instanceof Name) {
            return [];
        }

        if ($node->class->toString() !== ReflectionClass::class) {
            return [];
        }

        $argValue = $node->getArgs()[0]->value;
        $exprStaticType = $scope->getType($argValue);

        if (RectorAllowedAutoloadedTypeAnalyzer::isAllowedType($exprStaticType)) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::RECTOR_NO_CLASS_REFLECTION_STATIC_REFLECTION)
            ->build()];
    }
}
