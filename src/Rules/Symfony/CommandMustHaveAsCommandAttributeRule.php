<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Component\Console\Command\Command;
use Symplify\PHPStanRules\Rules\Enum\SymfonyAttribute;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\CommandMustHaveAsCommandAttributeRule\CommandMustHaveAsCommandAttributeRuleTest
 *
 * @implements Rule<Class_>
 */
final class CommandMustHaveAsCommandAttributeRule implements Rule
{
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;

    /**
     * @var string
     */
    private const LARAVEL_COMMAND_CLASS = 'Illuminate\Console\Command';

    /**
     * @var string
     */
    private const SYMFONY_CONSOLE_PACKAGE = 'symfony/console';

    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" extends Command but is missing the #[AsCommand] attribute';

    private ?bool $hasSymfonyConsolePackage = null;

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     *
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // an anonymous class carries no name to report, and is no registered command
        if (! $node->name instanceof Identifier) {
            return [];
        }

        // only projects that depend on symfony/console in composer.json
        if (! $this->hasSymfonyConsolePackage()) {
            return [];
        }

        // Laravel commands use $signature instead of the #[AsCommand] attribute
        if ($this->reflectionProvider->hasClass(self::LARAVEL_COMMAND_CLASS)) {
            return [];
        }

        // abstract base commands do not need the attribute
        if ($node->isAbstract()) {
            return [];
        }

        $className = (string) $node->namespacedName;
        if (! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        if (! $this->reflectionProvider->getClass($className)->is(Command::class)) {
            return [];
        }

        // do not require an attribute the project does not even have
        if (! $this->reflectionProvider->hasClass(SymfonyAttribute::AS_COMMAND)) {
            return [];
        }

        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($attr->name->toString() === SymfonyAttribute::AS_COMMAND) {
                    return [];
                }
            }
        }

        $identifierRuleError = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $className))
            ->identifier('symplify.commandHasAsCommandAttribute')
            ->build();

        return [$identifierRuleError];
    }

    private function hasSymfonyConsolePackage(): bool
    {
        if ($this->hasSymfonyConsolePackage !== null) {
            return $this->hasSymfonyConsolePackage;
        }

        $composerJsonFilePath = getcwd() . '/composer.json';
        if (! is_file($composerJsonFilePath)) {
            return $this->hasSymfonyConsolePackage = false;
        }

        $composerJsonContents = file_get_contents($composerJsonFilePath);
        if ($composerJsonContents === false) {
            return $this->hasSymfonyConsolePackage = false;
        }

        $composerJson = json_decode($composerJsonContents, true);
        if (! is_array($composerJson)) {
            return $this->hasSymfonyConsolePackage = false;
        }

        $requirePackages = $composerJson['require'] ?? [];
        if (is_array($requirePackages) && isset($requirePackages[self::SYMFONY_CONSOLE_PACKAGE])) {
            return $this->hasSymfonyConsolePackage = true;
        }

        $requireDevPackages = $composerJson['require-dev'] ?? [];
        if (is_array($requireDevPackages) && isset($requireDevPackages[self::SYMFONY_CONSOLE_PACKAGE])) {
            return $this->hasSymfonyConsolePackage = true;
        }

        return $this->hasSymfonyConsolePackage = false;
    }
}
