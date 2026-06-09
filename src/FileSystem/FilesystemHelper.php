<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\FileSystem;

final class FilesystemHelper
{
    public static function resolveFromCwd(string $filePath): string
    {
        // make path relative with native PHP
        $realPath = (string) realpath($filePath);
        $relativeFilePath = str_replace(getcwd() . DIRECTORY_SEPARATOR, '', $realPath);

        return rtrim($relativeFilePath, '/');
    }
}
