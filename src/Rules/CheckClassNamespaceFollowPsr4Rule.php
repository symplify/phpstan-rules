<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Composer\ClassNamespaceMatcher;
use Symplify\PHPStanRules\Composer\ComposerAutoloadResolver;
use Symplify\PHPStanRules\Composer\Psr4PathValidator;

/**
 * @implements Rule<ClassLike>
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckClassNamespaceFollowPsr4Rule\CheckClassNamespaceFollowPsr4RuleTest
 */
final class CheckClassNamespaceFollowPsr4Rule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class like namespace "%s" does not follow PSR-4 configuration in composer.json';

    /**
     * @var array<string, string|string[]>
     */
    private array $autoloadPsr4Paths = [];

    public function __construct(
        ComposerAutoloadResolver $composerAutoloadResolver,
        private readonly Psr4PathValidator $psr4PathValidator,
        private readonly ClassNamespaceMatcher $classNamespaceMatcher
    ) {
        $this->autoloadPsr4Paths = $composerAutoloadResolver->getPsr4Autoload();
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ClassLike::class;
    }

    /**
     * @param ClassLike $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->autoloadPsr4Paths === []) {
            return [];
        }

        $namespaceBeforeClass = $this->resolveNamespaceBeforeClass($node, $scope);
        if ($namespaceBeforeClass === null) {
            return [];
        }

        $filePath = str_replace('\\', '/', $scope->getFile());

        $possibleNamespacesToDirectories = $this->classNamespaceMatcher->matchPossibleDirectoriesForClass(
            $namespaceBeforeClass,
            $this->autoloadPsr4Paths,
            $scope
        );

        if ($possibleNamespacesToDirectories === []) {
            return [];
        }

        foreach ($possibleNamespacesToDirectories as $possibleNamespaceToDirectory) {
            if ($this->psr4PathValidator->isClassNamespaceCorrect($possibleNamespaceToDirectory, $filePath)) {
                return [];
            }
        }

        if ($namespaceBeforeClass === '') {
            return [];
        }

        $namespacePart = substr($namespaceBeforeClass, 0, -1);
        $errorMessage = sprintf(self::ERROR_MESSAGE, $namespacePart);

        return [RuleErrorBuilder::message($errorMessage)->identifier('check.classnamespacepsr4')->build()];
    }

    private function resolveNamespaceBeforeClass(ClassLike $classLike, Scope $scope): ?string
    {
        if (! $classLike->name instanceof Identifier) {
            return null;
        }

        return $scope->getNamespace() . '\\';
    }
}
