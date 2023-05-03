<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\ValueObject;

use PhpParser\Node\Expr;

final class TwoExprs
{
    /**
     * @readonly
     * @var \PhpParser\Node\Expr
     */
    private $firstExpr;
    /**
     * @readonly
     * @var \PhpParser\Node\Expr
     */
    private $secondExpr;
    public function __construct(Expr $firstExpr, Expr $secondExpr)
    {
        $this->firstExpr = $firstExpr;
        $this->secondExpr = $secondExpr;
    }

    public function getFirstExpr(): Expr
    {
        return $this->firstExpr;
    }

    public function getSecondExpr(): Expr
    {
        return $this->secondExpr;
    }
}
