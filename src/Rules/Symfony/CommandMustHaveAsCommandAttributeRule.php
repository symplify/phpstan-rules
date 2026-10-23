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
use Symplify\PHPStanRules\Composer\ComposerRequireResolver;
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
     * @readonly
     */
    private ComposerRequireResolver $composerRequireResolver;

    /**
     * @var string
     */
    private const SYMFONY_CONSOLE_PACKAGE = 'symfony/console';

    /**
     * @var string
     */
    private const LARAVEL_COMMAND_CLASS = 'Illuminate\Console\Command';

    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" extends Command but is missing the #[AsCommand] attribute';

    public function __construct(ReflectionProvider $reflectionProvider, ComposerRequireResolver $composerRequireResolver)
    {
        $this->reflectionProvider = $reflectionProvider;
        $this->composerRequireResolver = $composerRequireResolver;
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

        // do not require an attribute the project does not even have
        if (! $this->reflectionProvider->hasClass(SymfonyAttribute::AS_COMMAND)) {
            return [];
        }

        // symfony/console must be a direct production dependency, require-dev is skipped
        if (! $this->composerRequireResolver->hasRequiredPackage(self::SYMFONY_CONSOLE_PACKAGE)) {
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
}
