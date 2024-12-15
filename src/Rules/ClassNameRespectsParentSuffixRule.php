<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Exception;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPUnit\Framework\TestCase;
use Rector\Rector\AbstractRector;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Symplify\PHPStanRules\Naming\ClassToSuffixResolver;

/**
 * @implements Rule<InClassNode>
 * @see \Symplify\PHPStanRules\Tests\Rules\ClassNameRespectsParentSuffixRule\ClassNameRespectsParentSuffixRuleTest
 */
final class ClassNameRespectsParentSuffixRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class should have suffix "%s" to respect parent type';

    /**
     * @var string[]
     */
    private const DEFAULT_PARENT_CLASSES = [
        'Symfony\Component\Console\Command\Command',
        EventSubscriberInterface::class,
        AbstractController::class,
        Sniff::class,
        TestCase::class,
        Exception::class,
        'PhpCsFixer\Fixer\FixerInterface',
        Rule::class,
        AbstractRector::class,
    ];

    /**
     * @var string[]
     */
    private array $parentClasses = [];

    /**
     * @param class-string[] $parentClasses
     */
    public function __construct(
        private readonly ClassToSuffixResolver $classToSuffixResolver,
        array $parentClasses = [],
    ) {
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
            if (\str_ends_with($classReflection->getName(), $expectedSuffix)) {
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
