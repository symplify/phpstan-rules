<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject\Spotter;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

final class IfAndCondExpr
{
    /**
     * @var \PhpParser\Node\Stmt
     */
    private $stmt;
    /**
     * @var \PhpParser\Node\Expr|null
     */
    private $condExpr;
    /**
     * @param \PhpParser\Node\Expr|null $condExpr
     */
    public function __construct(Stmt $stmt, $condExpr)
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
