<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Composer;

final class ComposerRequireResolver
{
    /**
     * @readonly
     */
    private string $workingDirectory;

    public function __construct(string $workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
    }

    public function hasRequiredPackage(string $packageName): bool
    {
        $composerJsonFilePath = $this->workingDirectory . '/composer.json';
        if (! is_file($composerJsonFilePath)) {
            return false;
        }

        $composerJson = json_decode((string) file_get_contents($composerJsonFilePath), true);
        if (! is_array($composerJson)) {
            return false;
        }

        // only the "require" section counts, "require-dev" is skipped on purpose
        $require = $composerJson['require'] ?? [];
        if (! is_array($require)) {
            return false;
        }

        return isset($require[$packageName]);
    }
}
