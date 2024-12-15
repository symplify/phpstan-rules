<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        PHPUnitSetList::PHPUNIT_100,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        LevelSetList::UP_TO_PHP_81,
        SetList::CODING_STYLE,
        SetList::TYPE_DECLARATION,
        SetList::NAMING,
        SetList::PRIVATIZATION,
        SetList::EARLY_RETURN,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
    ]);

    $rectorConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->importNames();

    $rectorConfig->skip([
        '*/Source/*',
        '*/Fixture/*',

        \Rector\Php55\Rector\String_\StringClassNameToClassConstantRector::class => [
            __DIR__ . '/tests/Naming/ClassToSuffixResolverTest.php',
            __DIR__ . '/tests/Rules/Rector/PhpUpgradeImplementsMinPhpVersionInterfaceRule/PhpUpgradeImplementsMinPhpVersionInterfaceRuleTest.php',
        ],
    ]);

    $rectorConfig->ruleWithConfiguration(\Rector\Php55\Rector\String_\StringClassNameToClassConstantRector::class, [
        'Symfony\Component\Console\*',
        'Rector\Contract\Rector\ConfigurableRectorInterface',
    ]);
};
