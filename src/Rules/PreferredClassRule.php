<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreferredClassRule\PreferredClassRuleTest
 *
 * @implements Rule<Node>
 */
final class PreferredClassRule implements Rule
{
    /**
     * @var string[]
     * @readonly
     */
    private array $oldToPreferredClasses;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of "%s" class/interface use "%s"';

    /**
     * @param string[] $oldToPreferredClasses
     */
    public function __construct(array $oldToPreferredClasses)
    {
        $this->oldToPreferredClasses = $oldToPreferredClasses;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node instanceof New_) {
            return $this->processNew($node);
        }

        if ($node instanceof InClassNode) {
            return $this->processClass($node);
        }

        if ($node instanceof StaticCall || $node instanceof Instanceof_) {
            return $this->processExprWithClass($node);
        }

        if ($node instanceof Name) {
            return $this->processClassName($node->toString());
        }

        return [];
    }

    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function processNew(New_ $new): array
    {
        if (! $new->class instanceof Name) {
            return [];
        }

        $className = $new->class->toString();
        return $this->processClassName($className);
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function processClass(InClassNode $inClassNode): array
    {
        $classReflection = $inClassNode->getClassReflection();

        $parentClassReflection = $classReflection->getParentClass();
        if (! $parentClassReflection instanceof ClassReflection) {
            return [];
        }

        $className = $classReflection->getName();

        $parentClassName = $parentClassReflection->getName();
        foreach ($this->oldToPreferredClasses as $oldClass => $prefferedClass) {
            if ($parentClassName !== $oldClass) {
                continue;
            }

            // check special case, when new class is actually the one we use
            if ($prefferedClass === $className) {
                return [];
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $oldClass, $prefferedClass);
            return [RuleErrorBuilder::message($errorMessage)
                ->identifier(RuleIdentifier::PREFERRED_CLASS)
                ->build()];
        }

        return [];
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function processClassName(string $className): array
    {
        foreach ($this->oldToPreferredClasses as $oldClass => $prefferedClass) {
            if ($className !== $oldClass) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $oldClass, $prefferedClass);
            $ruleError = RuleErrorBuilder::message($errorMessage)
                ->identifier(RuleIdentifier::PREFERRED_CLASS)
                ->build();

            return [$ruleError];
        }

        return [];
    }

    /**
     * @return list<IdentifierRuleError>
     * @param \PhpParser\Node\Expr\StaticCall|\PhpParser\Node\Expr\Instanceof_ $node
     */
    private function processExprWithClass($node): array
    {
        if ($node->class instanceof Expr) {
            return [];
        }

        $className = (string) $node->class;
        return $this->processClassName($className);
    }
}
