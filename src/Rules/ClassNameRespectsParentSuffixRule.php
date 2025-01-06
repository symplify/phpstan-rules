<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Exception;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\ClassName;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Symplify\PHPStanRules\Naming\ClassToSuffixResolver;

/**
 * @implements Rule<InClassNode>
 * @see \Symplify\PHPStanRules\Tests\Rules\ClassNameRespectsParentSuffixRule\ClassNameRespectsParentSuffixRuleTest
 */
final class ClassNameRespectsParentSuffixRule implements Rule
{
    /**
     * @readonly
     */
    private ClassToSuffixResolver $classToSuffixResolver;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class should have suffix "%s" to respect parent type';

    /**
     * @var string[]
     */
    private const DEFAULT_PARENT_CLASSES = [
        'Symfony\Component\Console\Command\Command',
        ClassName::EVENT_SUBSCRIBER_INTERFACE,
        ClassName::SYMFONY_ABSTRACT_CONTROLLER,
        ClassName::SNIFF,
        ClassName::PHPUNIT_TEST_CASE,
        Exception::class,
        'PhpCsFixer\Fixer\FixerInterface',
        Rule::class,
        ClassName::ABSTRACT_RECTOR,
    ];

    /**
     * @var string[]
     */
    private array $parentClasses = [];

    /**
     * @param class-string[] $parentClasses
     */
    public function __construct(
        ClassToSuffixResolver $classToSuffixResolver,
        array $parentClasses = []
    ) {
        $this->classToSuffixResolver = $classToSuffixResolver;
        $this->parentClasses = array_merge($parentClasses, self::DEFAULT_PARENT_CLASSES);
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        $classReflection = $node->getClassReflection();
        if ($classReflection->isAbstract()) {
            return [];
        }

        if ($classReflection->isAnonymous()) {
            return [];
        }

        return $this->processClassNameAndShort($classReflection);
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function processClassNameAndShort(ClassReflection $classReflection): array
    {
        foreach ($this->parentClasses as $parentClass) {
            if (! $classReflection->isSubclassOf($parentClass)) {
                continue;
            }

            $expectedSuffix = $this->classToSuffixResolver->resolveFromClass($parentClass);
            if (substr_compare($classReflection->getName(), $expectedSuffix, -strlen($expectedSuffix)) === 0) {
                return [];
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $expectedSuffix);
            return [RuleErrorBuilder::message($errorMessage)
                ->identifier(RuleIdentifier::CLASS_NAME_RESPECTS_PARENT_SUFFIX)
                ->build()];
        }

        return [];
    }
}
