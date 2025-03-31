<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Doctrine;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Exception\ShouldNotHappenException;

final class RepositoryClassResolver
{
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;
    /**
     * @var string
     */
    private const QUOTED_REPOSITORY_CLASS_REGEX = '#repositoryClass=\"(?<repositoryClass>.*?)\"#';

    /**
     * @var string
     */
    private const REPOSITORY_CLASS_CONST_REGEX = '#repositoryClass=?(\\\\)(?<repositoryClass>.*?)::class#';

    /**
     * @var string
     */
    private const USE_REPOSITORY_REGEX = '#use (?<repositoryClass>.*?Repository);#';

    /**
     * @var string[]
     */
    private const REGEX_TRAIN = [
        self::QUOTED_REPOSITORY_CLASS_REGEX,
        self::REPOSITORY_CLASS_CONST_REGEX,
        self::USE_REPOSITORY_REGEX,
    ];

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function resolveFromEntityClass(string $entityClassName): ?string
    {
        if (! $this->reflectionProvider->hasClass($entityClassName)) {
            throw new ShouldNotHappenException(sprintf('Entity "%s" class was not found', $entityClassName));
        }

        $classReflection = $this->reflectionProvider->getClass($entityClassName);

        $entityClassFileName = $classReflection->getFileName();
        if ($entityClassFileName === null) {
            return null;
        }

        $entityFileContents = FileSystem::read($entityClassFileName);
        $repositoryClass = null;

        foreach (self::REGEX_TRAIN as $regex) {
            $match = Strings::match($entityFileContents, $regex);
            if ($match === null) {
                continue;
            }

            $repositoryClass = $match['repositoryClass'];
            break;
        }

        if ($repositoryClass === null) {
            return null;
        }

        if (! $this->reflectionProvider->hasClass($repositoryClass)) {
            $errorMessage = sprintf('Repository class "%s" for entity "%s" does not exist', $repositoryClass, $entityClassName);
            throw new ShouldNotHappenException($errorMessage);
        }

        return $repositoryClass;
    }
}
