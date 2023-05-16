<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject\Spotter;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

final class IfAndCondExpr
{
    /**
     * @readonly
     * @var \PhpParser\Node\Stmt
     */
    private $stmt;
    /**
     * @readonly
     * @var \PhpParser\Node\Expr|null
     */
    private $condExpr;
    public function __construct(Stmt $stmt, ?\PhpParser\Node\Expr $condExpr)
    {
        $this->stmt = $stmt;
        $this->condExpr = $condExpr;
    }

    public function getStmt(): Stmt
    {
        return $this->stmt;
    }

    public function getCondExpr(): ?Expr
    {
        return $this->condExpr;
    }
}
