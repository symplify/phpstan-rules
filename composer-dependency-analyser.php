<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration())
    ->addPathToExclude(__DIR__ . '/tests/Rules/Rector/NoInstanceOfStaticReflectionRule/Fixture')
    ->addPathToExclude(__DIR__ . '/tests/Rules/Enum/RequireUniqueEnumConstantRule/Fixture')
    ->addPathToExclude(__DIR__ . '/tests/Rules/ForbiddenExtendOfNonAbstractClassRule/Fixture')
    ->addPathToExclude(__DIR__ . '/tests/Rules/PHPUnit/NoTestMocksRule/Fixture')

    // optional classes
    ->ignoreUnknownClasses(['Symfony\Component\ExpressionLanguage\Expression'])

    // already in phpstan/phpstan
    ->ignoreErrorsOnPackage('nikic/php-parser', [ErrorType::DEV_DEPENDENCY_IN_PROD])
    ->ignoreErrorsOnPackage('symfony/routing', [ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnPackage('symfony/var-dumper', [ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnPackage('symfony/service-contracts', [ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnPackage('symfony/http-kernel', [ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnPackage('symfony/event-dispatcher', [ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnPackage('symfony/deprecation-contracts', [ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnPackage('symfony/dependency-injection', [ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnPackage('symfony/config', [ErrorType::SHADOW_DEPENDENCY])

    // rule that runs on Rector rule only
    ->ignoreErrorsOnPackage('rector/rector', [ErrorType::DEV_DEPENDENCY_IN_PROD])

    ->ignoreErrorsOnExtension('ext-simplexml', [ErrorType::SHADOW_DEPENDENCY]);
