<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use Doctrine\Common\Collections\Collection;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\ParserConfig;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Method with "Collection" return type must declare a generic docblock, e.g. "@return Collection<int, Entity>".
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionReturnMustHaveGenericDocblockRule\CollectionReturnMustHaveGenericDocblockRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final class CollectionReturnMustHaveGenericDocblockRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method "%s()" returns bare "Collection", add a generic docblock, e.g. "@return Collection<int, SomeEntity>"';

    /**
     * @readonly
     */
    private Lexer $lexer;

    /**
     * @readonly
     */
    private PhpDocParser $phpDocParser;

    public function __construct()
    {
        $parserConfig = new ParserConfig([]);
        $this->lexer = new Lexer($parserConfig);
        $constExprParser = new ConstExprParser($parserConfig);
        $this->phpDocParser = new PhpDocParser($parserConfig, new TypeParser($parserConfig, $constExprParser), $constExprParser);
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $returnType = $node->returnType instanceof NullableType ? $node->returnType->type : $node->returnType;

        if (! $returnType instanceof Name || $returnType->toString() !== Collection::class) {
            return [];
        }

        if ($this->hasGenericReturnDocblock($node)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $node->name->toString());

        return [
            RuleErrorBuilder::message($errorMessage)
                ->identifier('doctrine.collectionReturnMustHaveGenericDocblock')
                ->line($node->getStartLine())
                ->build(),
        ];
    }

    private function hasGenericReturnDocblock(ClassMethod $classMethod): bool
    {
        $docComment = $classMethod->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        $tokenIterator = new TokenIterator($this->lexer->tokenize($docComment->getText()));
        $phpDocNode = $this->phpDocParser->parse($tokenIterator);

        foreach ($phpDocNode->getReturnTagValues() as $returnTagValueNode) {
            if ($this->isGenericType($returnTagValueNode->type)) {
                return true;
            }
        }

        return false;
    }

    private function isGenericType(TypeNode $typeNode): bool
    {
        if ($typeNode instanceof GenericTypeNode) {
            return true;
        }

        if ($typeNode instanceof NullableTypeNode) {
            return $this->isGenericType($typeNode->type);
        }

        if ($typeNode instanceof UnionTypeNode) {
            foreach ($typeNode->types as $innerTypeNode) {
                if ($this->isGenericType($innerTypeNode)) {
                    return true;
                }
            }
        }

        return false;
    }
}
