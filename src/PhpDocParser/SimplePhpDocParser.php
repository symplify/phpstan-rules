<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDocParser;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;

final class SimplePhpDocParser
{
    /**
     * @readonly
     * @var \PHPStan\PhpDocParser\Parser\PhpDocParser
     */
    private $phpDocParser;
    /**
     * @readonly
     * @var \PHPStan\PhpDocParser\Lexer\Lexer
     */
    private $lexer;
    public function __construct(PhpDocParser $phpDocParser, Lexer $lexer)
    {
        $this->phpDocParser = $phpDocParser;
        $this->lexer = $lexer;
    }

    public function parseNode(Node $node): ?PhpDocNode
    {
        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return null;
        }

        return $this->parseDocBlock($docComment->getText());
    }

    private function parseDocBlock(string $docBlock): PhpDocNode
    {
        $tokens = $this->lexer->tokenize($docBlock);
        $tokenIterator = new TokenIterator($tokens);

        $phpDocNode = $this->phpDocParser->parse($tokenIterator);
        return new PhpDocNode($phpDocNode->children);
    }
}
