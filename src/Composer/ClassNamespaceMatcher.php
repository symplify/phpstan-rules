<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Composer;

use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Location\DirectoryChecker;
use Symplify\PHPStanRules\ValueObject\ClassNamespaceAndDirectory;

final class ClassNamespaceMatcher
{
    public function __construct(
        private readonly DirectoryChecker $directoryChecker
    ) {
    }

    /**
     * @param array<string, string|string[]> $autoloadPsr4Paths
     * @return ClassNamespaceAndDirectory[]
     */
    public function matchPossibleDirectoriesForClass(
        string $namespaceBeforeClass,
        array $autoloadPsr4Paths,
        Scope $scope
    ): array {
        $namespaceToDirectories = [];

        foreach ($autoloadPsr4Paths as $namespace => $directory) {
            $namespace = rtrim($namespace, '\\') . '\\';
            if ($namespaceBeforeClass === $namespace) {
                return [];
            }

            $directories = $this->standardizeToArray($directory);
            foreach ($directories as $directory) {
                if (! $this->directoryChecker->isInDirectoryNamed($scope, $directory)) {
                    continue;
                }

                $namespaceToDirectories[] = new ClassNamespaceAndDirectory(
                    $namespace,
                    $directory,
                    $namespaceBeforeClass
                );
                continue 2;
            }
        }

        return $namespaceToDirectories;
    }

    /**
     * @param string|string[] $items
     * @return string[]
     */
    private function standardizeToArray(string | array $items): array
    {
        if (! is_array($items)) {
            return [$items];
        }

        return $items;
    }
}
