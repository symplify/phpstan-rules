<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use Doctrine\Common\Collections\Collection;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
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
 * Method parameter typed "Collection" must declare a generic docblock, e.g. "@param Collection<int, Entity> $items".
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionParamMustHaveGenericDocblockRule\CollectionParamMustHaveGenericDocblockRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final class CollectionParamMustHaveGenericDocblockRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Parameter "$%s" of method "%s()" is bare "Collection", add a generic docblock, e.g. "@param Collection<int, SomeEntity> $%s"';

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
        $genericParamNames = $this->resolveGenericParamNames($node);

        $ruleErrors = [];

        foreach ($node->params as $param) {
            $paramType = $param->type instanceof NullableType ? $param->type->type : $param->type;

            if (! $paramType instanceof Name || $paramType->toString() !== Collection::class) {
                continue;
            }

            if (! $param->var instanceof Variable || ! is_string($param->var->name)) {
                continue;
            }

            if (in_array($param->var->name, $genericParamNames, true)) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $param->var->name, $node->name->toString(), $param->var->name);

            $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                ->identifier('doctrine.collectionParamMustHaveGenericDocblock')
                ->line($param->getStartLine())
                ->build();
        }

        return $ruleErrors;
    }

    /**
     * @return list<string>
     */
    private function resolveGenericParamNames(ClassMethod $classMethod): array
    {
        $docComment = $classMethod->getDocComment();
        if (! $docComment instanceof Doc) {
            return [];
        }

        $tokenIterator = new TokenIterator($this->lexer->tokenize($docComment->getText()));
        $phpDocNode = $this->phpDocParser->parse($tokenIterator);

        $genericParamNames = [];
        foreach ($phpDocNode->getParamTagValues() as $paramTagValueNode) {
            if ($paramTagValueNode instanceof ParamTagValueNode && $this->isGenericType($paramTagValueNode->type)) {
                $genericParamNames[] = ltrim($paramTagValueNode->parameterName, '$');
            }
        }

        return $genericParamNames;
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
