<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc\PhpDocNodeTraverser;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\Reflection\ClassReflection;
use Symplify\PHPStanRules\PhpDoc\ClassReferencePhpDocNodeVisitor;
use Symplify\PHPStanRules\PhpDocParser\PhpDocNodeTraverser;

final class ClassReferencePhpDocNodeTraverser
{
    /**
     * @readonly
     * @var \Symplify\PHPStanRules\PhpDoc\ClassReferencePhpDocNodeVisitor
     */
    private $classReferencePhpDocNodeVisitor;
    public function __construct(ClassReferencePhpDocNodeVisitor $classReferencePhpDocNodeVisitor)
    {
        $this->classReferencePhpDocNodeVisitor = $classReferencePhpDocNodeVisitor;
    }

    public function decoratePhpDocNode(PhpDocNode $simplePhpDocNode, ClassReflection $classReflection): void
    {
        $phpDocNodeTraverser = new PhpDocNodeTraverser();

        $this->classReferencePhpDocNodeVisitor->configureClassName($classReflection->getName());
        $phpDocNodeTraverser->addPhpDocNodeVisitor($this->classReferencePhpDocNodeVisitor);

        $phpDocNodeTraverser->traverse($simplePhpDocNode);
    }
}
