<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use Doctrine\Common\Collections\Collection;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
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
 * Property typed "Collection" must declare a generic docblock, e.g. "@var Collection<int, Entity>".
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionPropertyMustHaveGenericDocblockRule\CollectionPropertyMustHaveGenericDocblockRuleTest
 *
 * @implements Rule<Property>
 */
final class CollectionPropertyMustHaveGenericDocblockRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Property "$%s" is bare "Collection", add a generic docblock, e.g. "@var Collection<int, SomeEntity>"';

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
        return Property::class;
    }

    /**
     * @param Property $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $type = $node->type instanceof NullableType ? $node->type->type : $node->type;

        if (! $type instanceof Name || $type->toString() !== Collection::class) {
            return [];
        }

        if ($this->hasGenericVarDocblock($node)) {
            return [];
        }

        $propertyName = $node->props[0]->name->toString();

        $errorMessage = sprintf(self::ERROR_MESSAGE, $propertyName);

        return [
            RuleErrorBuilder::message($errorMessage)
                ->identifier('doctrine.collectionPropertyMustHaveGenericDocblock')
                ->line($node->getStartLine())
                ->build(),
        ];
    }

    private function hasGenericVarDocblock(Property $property): bool
    {
        $docComment = $property->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        $tokenIterator = new TokenIterator($this->lexer->tokenize($docComment->getText()));
        $phpDocNode = $this->phpDocParser->parse($tokenIterator);

        foreach ($phpDocNode->getVarTagValues() as $varTagValueNode) {
            if ($this->isGenericType($varTagValueNode->type)) {
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

        if ($typeNode instanceof UnionTypeNode) {
            foreach ($typeNode->types as $innerTypeNode) {
                if ($innerTypeNode instanceof GenericTypeNode) {
                    return true;
                }
            }
        }

        return false;
    }
}
