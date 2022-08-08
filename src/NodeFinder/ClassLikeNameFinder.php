<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use Nette\Utils\Strings;
use Symfony\Component\Finder\Finder;
use Symplify\PHPStanRules\Composer\ComposerAutoloadResolver;
use Symplify\PHPStanRules\Composer\ComposerVendorAutoloadResolver;
use Symplify\PHPStanRules\Matcher\ClassLikeNameMatcher;

/**
 * @see \Symplify\PHPStanRules\Tests\NodeFinder\ClassLikeNameFinderTest
 */
final class ClassLikeNameFinder
{
    /**
     * @see https://regex101.com/r/Ayh6S2/1
     * @var string
     */
    private const EXTRACT_NAMESPACE_REGEX = '#namespace\\s+(?<namespace>[A-Za-z0-9\\\\]+?)\\s*;#sm';

    /**
     * @var array<string,string[]>
     */
    private static $cache = [];

    /**
     * @var array<string, string|string[]>
     */
    private $autoloadPsr4Paths = [];
    /**
     * @var \Symplify\PHPStanRules\Matcher\ClassLikeNameMatcher
     */
    private $classLikeNameMatcher;
    public function __construct(ClassLikeNameMatcher $classLikeNameMatcher, ComposerAutoloadResolver $composerAutoloadResolver, ComposerVendorAutoloadResolver $composerVendorAutoloadResolver)
    {
        $this->classLikeNameMatcher = $classLikeNameMatcher;
        $this->autoloadPsr4Paths = array_merge($composerAutoloadResolver->getPsr4Autoload(), $composerVendorAutoloadResolver->getPsr4Autoload());
    }

    /**
     * Works with projects which respect PSR4 standard, iterates the smallest possible amount of directories / files
     * based on namespace pattern
     *
     * @return string[]|mixed[]
     */
    public function getClassLikeNamesMatchingNamespacePattern(string $namespacePattern): array
    {
        if (isset(self::$cache[$namespacePattern])) {
            return self::$cache[$namespacePattern];
        }

        $narrowedNamespace = $this->getNarrowedNamespaceForSearch($namespacePattern);
        $possibleDirectories = $this->getPossibleDirectoriesForNamespace($narrowedNamespace);
        $keepExistingDirectoriesCallback = static function (string $directory) : bool {
            return is_dir($directory);
        };
        $filteredPossibleDirectories = array_filter($possibleDirectories, $keepExistingDirectoriesCallback);

        if ($filteredPossibleDirectories === []) {
            return [];
        }

        $finderFiles = Finder::create()->files()->in($filteredPossibleDirectories)->name('*.php');
        $classLikeNames = [];

        foreach ($finderFiles as $finderFile) {
            $realPath = $finderFile->getRealpath();
            $classLikeName = basename($realPath, '.php');

            $src = file_get_contents($realPath);
            if (! $src) {
                continue;
            }

            $namespace = $this->getNamespaceFromSrc($src);
            if ($namespace !== null) {
                $classLikeName = sprintf('%s\\%s', $namespace, $classLikeName);
            }

            if ($this->classLikeNameMatcher->isClassLikeNameMatchedAgainstPattern($classLikeName, $namespacePattern)) {
                $classLikeNames[] = $classLikeName;
            }
        }

        self::$cache[$namespacePattern] = $classLikeNames;

        return $classLikeNames;
    }

    private function getNamespaceFromSrc(string $src): ?string
    {
        $matches = Strings::match($src, self::EXTRACT_NAMESPACE_REGEX);
        if ($matches) {
            return $matches['namespace'];
        }

        return null;
    }

    private function getNarrowedNamespaceForSearch(string $namespacePattern): string
    {
        $isNarrowed = false;
        $namespacePatternParts = explode('\\', $namespacePattern);
        $namespacePatternPartsBeforeVariable = [];
        foreach ($namespacePatternParts as $namespacePatternPart) {
            if (strpos($namespacePatternPart, '*') !== false || strpos($namespacePatternPart, '?') !== false) {
                $isNarrowed = true;
                break;
            }

            $namespacePatternPartsBeforeVariable[] = $namespacePatternPart;
        }

        $narrowedNamespace = implode('\\', $namespacePatternPartsBeforeVariable);
        if ($isNarrowed && $narrowedNamespace !== '') {
            $narrowedNamespace .= '\\';
        }

        return $narrowedNamespace;
    }

    /**
     * @return string[]
     */
    private function getPossibleDirectoriesForNamespace(string $narrowedNamespace): array
    {
        $narrowedNamespaceIsEmpty = $narrowedNamespace === '';
        $possibleDirectories = [];
        $narrowestNamespaceLength = 0;
        foreach ($this->autoloadPsr4Paths as $namespace => $directories) {
            if ($narrowedNamespaceIsEmpty || strncmp($narrowedNamespace, $namespace, strlen($namespace)) === 0) {
                if (! $narrowedNamespaceIsEmpty) {
                    $namespaceLength = strlen($namespace);
                    if ($narrowestNamespaceLength < $namespaceLength) {
                        $narrowestNamespaceLength = $namespaceLength;
                        $possibleDirectories = [];
                    } else {
                        continue;
                    }
                }

                $directories = is_array($directories) ? $directories : [$directories];
                foreach ($directories as $directory) {
                    $possibleDirectories[] = $directory . DIRECTORY_SEPARATOR . str_replace(
                        '\\',
                        DIRECTORY_SEPARATOR,
                        substr($narrowedNamespace, strlen($namespace))
                    );
                }
            }
        }

        return $possibleDirectories;
    }
}
