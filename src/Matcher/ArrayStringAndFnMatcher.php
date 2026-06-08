<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Matcher;

/**
 * @api
 */
final class ArrayStringAndFnMatcher
{
    /**
     * @param string[] $matchingValues
     */
    public function isMatchWithIsA(string $currentValue, array $matchingValues): bool
    {
        if ($this->isMatch($currentValue, $matchingValues)) {
            return true;
        }

        return array_any($matchingValues, fn ($matchingValue): bool => is_a($currentValue, $matchingValue, true));
    }

    /**
     * @param string[] $matchingValues
     */
    public function isMatch(string $currentValue, array $matchingValues): bool
    {
        foreach ($matchingValues as $matchingValue) {
            if ($currentValue === $matchingValue) {
                return true;
            }

            if (fnmatch($matchingValue, $currentValue)) {
                return true;
            }

            if (fnmatch($matchingValue, $currentValue, FNM_NOESCAPE)) {
                return true;
            }
        }

        return false;
    }
}
