<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassMethod;
use Symplify\PHPStanRules\Enum\ClassName;

final class SymfonyRequiredMethodAnalyzer
{
    public static function detect(ClassMethod $classMethod): bool
    {
        // speed up
        if (! $classMethod->isPublic()) {
            return false;
        }

        if ($classMethod->isMagic()) {
            return false;
        }

        foreach ($classMethod->getAttrGroups() as $attributeGroup) {
            foreach ($attributeGroup->attrs as $attr) {
                if ($attr->name->toString() === ClassName::REQUIRED_ATTRIBUTE) {
                    return true;
                }
            }
        }

        $docComment = $classMethod->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        return str_contains($docComment->getText(), '@required');
    }
}
