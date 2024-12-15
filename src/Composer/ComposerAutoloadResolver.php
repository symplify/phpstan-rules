<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Composer;

use Symplify\PHPStanRules\FileSystem\FileSystem;

final class ComposerAutoloadResolver
{
    /**
     * @var string
     */
    private const COMPOSER_JSON_FILE = './composer.json';

    /**
     * @return array<string, string[]|string>
     */
    public function getPsr4Autoload(): array
    {
        if (! file_exists(self::COMPOSER_JSON_FILE)) {
            return [];
        }

        $fileContent = FileSystem::read(self::COMPOSER_JSON_FILE);
        $composerJsonContent = json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);

        $autoloadPsr4 = $composerJsonContent['autoload']['psr-4'] ?? [];
        $autoloadDevPsr4 = $composerJsonContent['autoload-dev']['psr-4'] ?? [];

        return array_merge($autoloadPsr4, $autoloadDevPsr4);
    }
}
