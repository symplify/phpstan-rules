<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony\ConfigClosure;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PhpParser\NodeVisitor;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyClosureDetector;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\FileNameMatchesExtensionRule\FileNameMatchesExtensionRuleTest
 * @implements Rule<Closure>
 */
final class FileNameMatchesExtensionRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'The config uses "%s" extension, but the file name is "%s". Sync them to ease discovery';

    public function getNodeType(): string
    {
        return Closure::class;
    }

    /**
     * @param Closure $node
     * @return IdentifierRuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! SymfonyClosureDetector::detect($node)) {
            return [];
        }

        $extensionName = $this->findExtensionName($node);
        if (! is_string($extensionName)) {
            return [];
        }

        // get basefile name
        $baseFileName = basename($scope->getFile(), '.php');
        if ($baseFileName === $extensionName) {
            return [];
        }

        // find if uses extension and get the name if so

        $identifierRuleError = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $extensionName, $baseFileName))
            ->identifier(SymfonyRuleIdentifier::FILE_NAME_MATCHES_EXTENSION)
            ->build();

        return [$identifierRuleError];
    }

    /**
     * @param \PhpParser\Node\Expr\Closure|\PhpParser\Node $node
     */
    private function findExtensionName($node): ?string
    {
        $extensionName = null;

        $nodeFinder = new NodeFinder();
        $nodeFinder->find($node, function (Node $node) use (&$extensionName): ?int {
            if (! $node instanceof MethodCall) {
                return null;
            }

            if (! $node->name instanceof Identifier) {
                return null;
            }

            $methodName = $node->name->toString();
            if ($methodName !== 'extension') {
                return null;
            }

            foreach ($node->getArgs() as $arg) {
                if (! $arg->value instanceof String_) {
                    continue;
                }

                $extensionName = $arg->value->value;
            }

            return NodeVisitor::STOP_TRAVERSAL;
        });

        return $extensionName;
    }
}
