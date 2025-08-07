<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;

return RectorConfig::configure()
    ->withPhpSets()
    ->withPreparedSets(deadCode: true, codeQuality: true, codingStyle: true, typeDeclarations: true, privatization: true, naming: true, earlyReturn: true, phpunitCodeQuality: true)
    ->withPaths([__DIR__ . '/config', __DIR__ . '/src', __DIR__ . '/tests'])
    ->withRootFiles()
    ->withImportNames()
    ->withSkip([
        '*/Source/*',
        '*/Fixture/*',
        StringClassNameToClassConstantRector::class => [
            __DIR__ . '/src/Symfony/NodeAnalyzer/SymfonyControllerAnalyzer.php',
            __DIR__ . '/tests/Naming/ClassToSuffixResolverTest.php',
            __DIR__ . '/tests/Rules/Rector/PhpUpgradeImplementsMinPhpVersionInterfaceRule/PhpUpgradeImplementsMinPhpVersionInterfaceRuleTest.php',
        ],
    ]);
