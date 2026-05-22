<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony\ConfigClosure;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;

/**
 * @implements Rule<MethodCall>
 */
final class NoSetClassServiceDuplicationRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of "$services->set(%s)->class(%s)" that brings no value, use simple $services->set(%s)';

    /**
     * @readonly
     */
    private Standard $standard;

    public function __construct()
    {
        $this->standard = new Standard();
    }

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->isFirstClassCallable()) {
            return [];
        }

        // parent method must be a method call too
        if (! $node->var instanceof MethodCall) {
            return [];
        }

        if (! $this->isMethodName($node->name, 'class')) {
            return [];
        }

        $parentMethodCall = $node->var;
        if (! $this->isMethodName($parentMethodCall->name, 'set')) {
            return [];
        }

        $parentSoleArgContents = $this->resolveSoleArgContents($parentMethodCall);
        if ($parentSoleArgContents === null) {
            return [];
        }

        $currentSoleArgContents = $this->resolveSoleArgContents($node);
        if ($currentSoleArgContents === null) {
            return [];
        }

        if ($parentSoleArgContents !== $currentSoleArgContents) {
            return [];
        }

        if (strpos($parentSoleArgContents, '\\') !== false) {
            $shortClassName = Strings::after($parentSoleArgContents, '\\', -1);
        } else {
            $shortClassName = $parentSoleArgContents;
        }

        $errorMessage = sprintf(
            self::ERROR_MESSAGE,
            $shortClassName,
            $shortClassName,
            $shortClassName
        );

        $identifierRuleError = RuleErrorBuilder::message($errorMessage)
            ->identifier(SymfonyRuleIdentifier::NO_SET_CLASS_SERVICE_DUPLICATE)
            ->build();

        return [$identifierRuleError];
    }

    private function isMethodName(Node $node, string $name): bool
    {
        if (! $node instanceof Identifier) {
            return false;
        }

        return $node->toString() === $name;
    }

    private function resolveSoleArgContents(MethodCall $methodCall): ?string
    {
        if (count($methodCall->getArgs()) !== 1) {
            return null;
        }

        $firstArgExpr = $methodCall->getArgs()[0]
            ->value;
        return $this->standard->prettyPrintExpr($firstArgExpr);
    }
}
