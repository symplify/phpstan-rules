<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration())->addPathToScan(__DIR__ . '/src', false)
    ->addPathToExclude(__DIR__ . '/tests/Rules/Rector/NoInstanceOfStaticReflectionRule/Fixture')
    ->addPathToExclude(__DIR__ . '/tests/Rules/Enum/RequireUniqueEnumConstantRule/Fixture')
    ->addPathToExclude(__DIR__ . '/tests/Rules/ForbiddenExtendOfNonAbstractClassRule/Fixture')
    ->addPathToExclude(__DIR__ . '/tests/Rules/PHPUnit/NoTestMocksRule/Fixture')

    // already in phpstan/phpstan
    ->ignoreErrorsOnPackage('nikic/php-parser', [ErrorType::DEV_DEPENDENCY_IN_PROD])

    // rule that runs on Rector rule only
    ->ignoreErrorsOnPackage('rector/rector', [ErrorType::DEV_DEPENDENCY_IN_PROD])

    ->ignoreErrorsOnExtension('ext-ctype', [ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnExtension('ext-simplexml', [ErrorType::SHADOW_DEPENDENCY]);

// ->ignoreErrorsOnPath('/Fixture/', [\ShipMonk\ComposerDependencyAnalyser\Config\ErrorType::UNKNOWN_CLASS]);
