<?php

namespace Doctrine\ORM;

if (class_exists('Doctrine\ORM\QueryBuilder')) {
    return;
}

class QueryBuilder
{
    public function select($select = null): self
    {
    }

    public function from(string $from, string $alias, ?string $indexBy = null): self
    {
    }

    public function update(?string $update = null, ?string $alias = null): self
    {
    }

    public function delete(?string $delete = null, ?string $alias = null): self
    {
    }

    public function where($predicates): self
    {
    }

    public function leftJoin($join, string $alias): self
    {
    }

    public function getDQL(): string
    {
    }
}
