<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\PhpDoc\Tag\DeprecatedTag;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\ClassName;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Symplify\PHPStanRules\PhpDoc\PhpDocResolver;
use Symplify\PHPStanRules\PhpDoc\SeePhpDocTagNodesFinder;

/**
 * @implements Rule<InClassNode>
 * @see \Symplify\PHPStanRules\Tests\Rules\SeeAnnotationToTestRule\SeeAnnotationToTestRuleTest
 */
final class SeeAnnotationToTestRule implements Rule
{
    /**
     * @readonly
     */
    private PhpDocResolver $phpDocResolver;
    /**
     * @readonly
     */
    private SeePhpDocTagNodesFinder $seePhpDocTagNodesFinder;
    /**
     * @var string[]
     * @readonly
     */
    private array $requiredSeeTypes;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" is missing @see annotation with test case class reference';

    /**
     * @param string[] $requiredSeeTypes
     */
    public function __construct(PhpDocResolver $phpDocResolver, SeePhpDocTagNodesFinder $seePhpDocTagNodesFinder, array $requiredSeeTypes)
    {
        $this->phpDocResolver = $phpDocResolver;
        $this->seePhpDocTagNodesFinder = $seePhpDocTagNodesFinder;
        $this->requiredSeeTypes = $requiredSeeTypes;
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
        $classReflection = $node->getClassReflection();
        if ($this->shouldSkipClassReflection($classReflection)) {
            return [];
        }

        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        $docComment = $node->getDocComment();
        $errorMessage = sprintf(self::ERROR_MESSAGE, $classReflection->getName());

        if (! $docComment instanceof Doc) {
            return [RuleErrorBuilder::message($errorMessage)
                ->identifier(RuleIdentifier::SEE_ANNOTATION_TO_TEST)
                ->build()];
        }

        $resolvedPhpDocBlock = $this->phpDocResolver->resolve($scope, $classReflection, $docComment);

        // skip deprecated
        $deprecatedTags = $resolvedPhpDocBlock->getDeprecatedTag();
        if ($deprecatedTags instanceof DeprecatedTag) {
            return [];
        }

        $seeTags = $this->seePhpDocTagNodesFinder->find($resolvedPhpDocBlock);
        if ($this->hasSeeTestCaseAnnotation($seeTags)) {
            return [];
        }

        return [RuleErrorBuilder::message($errorMessage)
            ->identifier(RuleIdentifier::SEE_ANNOTATION_TO_TEST)
            ->build()];
    }

    private function shouldSkipClassReflection(ClassReflection $classReflection): bool
    {
        if ($classReflection->isAbstract()) {
            return true;
        }

        foreach ($this->requiredSeeTypes as $requiredSeeType) {
            if ($classReflection->isSubclassOf($requiredSeeType)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param PhpDocTagNode[] $seeTags
     */
    private function hasSeeTestCaseAnnotation(array $seeTags): bool
    {
        foreach ($seeTags as $seeTag) {
            if (! $seeTag->value instanceof GenericTagValueNode) {
                continue;
            }

            if (is_a($seeTag->value->value, ClassName::PHPUNIT_TEST_CASE, true)) {
                return true;
            }
        }

        return false;
    }
}
