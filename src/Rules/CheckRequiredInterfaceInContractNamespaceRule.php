<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckRequiredInterfaceInContractNamespaceRule\CheckRequiredInterfaceInContractNamespaceRuleTest
 * @implements Rule<Interface_>
 */
final class CheckRequiredInterfaceInContractNamespaceRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Interface must be located in "Contract" or "Contracts" namespace';

    /**
     * @var string
     * @see https://regex101.com/r/kmrIG1/2
     */
    private const A_CONTRACT_NAMESPACE_REGEX = '#\bContracts?\b#';

    public function getNodeType(): string
    {
        return Interface_::class;
    }

    /**
     * @param Interface_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();
        if ($namespace === null) {
            return [];
        }

        if (Strings::match($namespace, self::A_CONTRACT_NAMESPACE_REGEX)) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)->build()];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
namespace App\Repository;

interface ProductRepositoryInterface
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
namespace App\Contract\Repository;

interface ProductRepositoryInterface
{
}
CODE_SAMPLE
            ),
        ]);
    }
}
