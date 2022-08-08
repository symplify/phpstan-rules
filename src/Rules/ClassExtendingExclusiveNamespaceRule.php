<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Json;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Matcher\ClassLikeNameMatcher;
use Symplify\PHPStanRules\NodeFinder\ClassLikeNameFinder;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ClassExtendingExclusiveNamespaceRule\ClassExtendingExclusiveNamespaceRuleTest
 */
final class ClassExtendingExclusiveNamespaceRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" is authorized to exist in one of the following namespaces: %s, but it is in namespace "%s". Please move it to one of the authorized namespaces.';
    /**
     * @var \Symplify\PHPStanRules\Matcher\ClassLikeNameMatcher
     */
    private $classLikeNameMatcher;
    /**
     * @var \Symplify\PHPStanRules\NodeFinder\ClassLikeNameFinder
     */
    private $classLikeNameFinder;
    /**
     * @var array<string, array<string>>
     */
    private $guards;

    /**
     * @param array<string, array<string>> $guards
     */
    public function __construct(ClassLikeNameMatcher $classLikeNameMatcher, ClassLikeNameFinder $classLikeNameFinder, array $guards)
    {
        $this->classLikeNameMatcher = $classLikeNameMatcher;
        $this->classLikeNameFinder = $classLikeNameFinder;
        $this->guards = $guards;
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();
        $classLikeName = $classReflection->getName();

        $namespace = $scope->getNamespace();
        if ($namespace === null) {
            return [];
        }

        foreach ($this->guards as $guardedTypeOrNamespacePattern => $allowedNamespacePatterns) {
            if (! $this->isSubjectToGuardedTypeOrNamespacePattern($classReflection, $guardedTypeOrNamespacePattern)) {
                continue;
            }

            if (! $this->isInAllowedNamespace($allowedNamespacePatterns, $classLikeName)) {
                $nativeReflectionClass = $classReflection->getNativeReflection();
                $errorMessage = sprintf(self::ERROR_MESSAGE, $classLikeName, Json::encode($allowedNamespacePatterns, 0), $nativeReflectionClass->getNamespaceName());

                return [$errorMessage];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Define in which namespaces (using *, ** or ? glob-like pattern matching) can classes extending specified class or implementing specified interface exist', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
namespace App;

// AbstractType implements \Symfony\Component\Form\FormTypeInterface
use Symfony\Component\Form\AbstractType;

class UserForm extends AbstractType
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
namespace App\Form;

use Symfony\Component\Form\AbstractType;

class UserForm extends AbstractType
{
}
CODE_SAMPLE
                ,
                [
                    'guards' => [
                        'Symfony\Component\Form\FormTypeInterface' => ['App\Form\**'],
                    ],
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
namespace App\Services;

use App\Component\PriceEngine\PriceProviderInterface;

class CustomerProductProvider extends PriceProviderInterface
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
namespace App\Component\PriceEngineImpl;

use App\Component\PriceEngine\PriceProviderInterface;

class CustomerProductProvider extends PriceProviderInterface
{
}
CODE_SAMPLE
                ,
                [
                    'guards' => [
                        'App\Component\PriceEngine\**' => [
                            'App\Component\PriceEngine\**',
                            'App\Component\PriceEngineImpl\**',
                        ],
                    ],
                ]
            ),
        ]);
    }

    private function isSubjectToGuardedTypeOrNamespacePattern(ClassReflection $classReflection, string $guardedTypeOrNamespacePattern): bool
    {
        $isGuardedSubjectNamespacePattern = strpos($guardedTypeOrNamespacePattern, '*') !== false || strpos($guardedTypeOrNamespacePattern, '?') !== false;
        $isGuardedSubjectType = ! $isGuardedSubjectNamespacePattern;
        if ($isGuardedSubjectType && ! $classReflection->isSubclassOf($guardedTypeOrNamespacePattern)) {
            return false;
        }
        if (! $isGuardedSubjectNamespacePattern) {
            return true;
        }
        return $this->isSubjectSubclassOfGuardedPattern($guardedTypeOrNamespacePattern, $classReflection);
    }

    private function isSubjectSubclassOfGuardedPattern(
        string $guardedTypeOrNamespacePattern,
        ClassReflection $classReflection
    ): bool {
        $classLikeNames = $this->classLikeNameFinder->getClassLikeNamesMatchingNamespacePattern(
            $guardedTypeOrNamespacePattern
        );
        foreach ($classLikeNames as $classLikeName) {
            if ($classReflection->isSubclassOf($classLikeName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $allowedNamespacePatterns
     */
    private function isInAllowedNamespace($allowedNamespacePatterns, string $classLikeName): bool
    {
        foreach ($allowedNamespacePatterns as $allowedNamespacePattern) {
            if ($this->classLikeNameMatcher->isClassLikeNameMatchedAgainstPattern(
                $classLikeName,
                $allowedNamespacePattern
            )) {
                return true;
            }
        }

        return false;
    }
}
