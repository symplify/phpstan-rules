<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony\ConfigClosure;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Twig\Extension\ExtensionInterface;

/**
 * Reports a config-closure tag that autoconfigure() adds on its own, e.g.
 * $services->set(SomeValidator::class)->tag('validator.constraint_validator').
 *
 * When the closure's defaults() opens with autoconfigure(), a service implementing ConstraintValidatorInterface is
 * tagged either way, so the tag repeats what the interface already says. Only a tag with no attributes of its own is
 * reported - an attribute (a priority, a validator alias, ...) says more than autoconfigure() does and keeps the tag.
 * A file with no autoconfigure() is left alone, there the tag is the only thing registering the service.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoAutoconfiguredServiceTagRule\NoAutoconfiguredServiceTagRuleTest
 *
 * @implements Rule<FileNode>
 */
final readonly class NoAutoconfiguredServiceTagRule implements Rule
{
    public const string ERROR_MESSAGE = 'Tag "%s" is added by autoconfigure() on its own, as "%s" is a %s - remove the ->tag() call';

    private const string SERVICES_VARIABLE_NAME = 'services';

    /**
     * The tags Symfony adds on its own to a service of the given type.
     *
     * @var array<string, string>
     */
    private const array AUTOCONFIGURED_TAGS = [
        'console.command' => Command::class,
        'form.type' => 'Symfony\Component\Form\FormTypeInterface',
        'kernel.event_subscriber' => EventSubscriberInterface::class,
        'security.voter' => 'Symfony\Component\Security\Core\Authorization\Voter\VoterInterface',
        'twig.extension' => ExtensionInterface::class,
        'validator.constraint_validator' => 'Symfony\Component\Validator\ConstraintValidatorInterface',
    ];

    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {
    }

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    /**
     * @param FileNode $node
     *
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $nodeFinder = new NodeFinder();

        /** @var MethodCall[] $methodCalls */
        $methodCalls = $nodeFinder->findInstanceOf($node->getNodes(), MethodCall::class);

        if (! $this->hasAutoconfigureCall($methodCalls)) {
            return [];
        }

        $ruleErrors = [];

        foreach ($methodCalls as $methodCall) {
            $tagName = $this->matchTagNameWithoutAttributes($methodCall);
            if ($tagName === null) {
                continue;
            }

            $autoconfiguredType = self::AUTOCONFIGURED_TAGS[$tagName] ?? null;
            if ($autoconfiguredType === null) {
                continue;
            }

            $className = $this->resolveTaggedClassName($methodCall);
            if ($className === null) {
                continue;
            }

            if (! $this->isSubclassOf($className, $autoconfiguredType)) {
                continue;
            }

            $ruleErrors[] = RuleErrorBuilder::message(
                sprintf(self::ERROR_MESSAGE, $tagName, $className, $autoconfiguredType)
            )
                ->identifier(SymfonyRuleIdentifier::NO_AUTOCONFIGURED_SERVICE_TAG)
                // the name, not the call - a chained call starts on the line of the $services->set() above it
                ->line($methodCall->name->getStartLine())
                ->build();
        }

        return $ruleErrors;
    }

    /**
     * @param MethodCall[] $methodCalls
     */
    private function hasAutoconfigureCall(array $methodCalls): bool
    {
        return array_any(
            $methodCalls,
            static fn (MethodCall $methodCall): bool => $methodCall->name instanceof Identifier && $methodCall->name->toString() === 'autoconfigure'
        );
    }

    /**
     * @return string|null the tag name of a ->tag() call that adds no attributes of its own
     */
    private function matchTagNameWithoutAttributes(MethodCall $methodCall): ?string
    {
        if (! $methodCall->name instanceof Identifier || $methodCall->name->toString() !== 'tag') {
            return null;
        }

        $args = $methodCall->getArgs();
        if (count($args) !== 1) {
            return null;
        }

        return $args[0]->value instanceof String_ ? $args[0]->value->value : null;
    }

    /**
     * Walks the call chain down to the $services->set() or $services->get() call the tag belongs to.
     */
    private function resolveTaggedClassName(MethodCall $methodCall): ?string
    {
        $currentCall = $methodCall->var;

        while ($currentCall instanceof MethodCall) {
            if ($currentCall->var instanceof Variable && $currentCall->var->name === self::SERVICES_VARIABLE_NAME) {
                if (! $currentCall->name instanceof Identifier || ! in_array($currentCall->name->toString(), ['set', 'get'], true)) {
                    return null;
                }

                $args = $currentCall->getArgs();

                return $args === [] ? null : $this->matchClassName($args[0]->value);
            }

            $currentCall = $currentCall->var;
        }

        return null;
    }

    private function matchClassName(Node $classValue): ?string
    {
        if (! $classValue instanceof ClassConstFetch || ! $classValue->class instanceof Name) {
            return null;
        }

        if (! $classValue->name instanceof Identifier || $classValue->name->toLowerString() !== 'class') {
            return null;
        }

        return $classValue->class->toString();
    }

    private function isSubclassOf(string $className, string $parentClassName): bool
    {
        if (! $this->reflectionProvider->hasClass($className)) {
            return false;
        }

        return $this->reflectionProvider->getClass($className)
            ->is($parentClassName);
    }
}
