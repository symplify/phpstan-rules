<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class EnumConstantAnalyzer
{
    public function isNonEnumConstantPrefix(string $prefix): bool
    {
        // constant prefix is needed
        if (substr_compare($prefix, '_', -strlen('_')) !== 0) {
            return true;
        }

        return $this->isNonEnumConstantName($prefix);
    }

    private function isNonEnumConstantName(string $name): bool
    {
        // not enum, but rather validation limit
        if (strncmp($name, 'MIN_', strlen('MIN_')) === 0) {
            return true;
        }

        if (substr_compare($name, '_MIN', -strlen('_MIN')) === 0) {
            return true;
        }

        if (strncmp($name, 'MAX_', strlen('MAX_')) === 0) {
            return true;
        }

        return substr_compare($name, '_MAX', -strlen('_MAX')) === 0;
    }
}
