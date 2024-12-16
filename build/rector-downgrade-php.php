<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Removing\Rector\Class_\RemoveInterfacesRector;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;

return RectorConfig::configure()
    ->withDowngradeSets(php74: true)
    ->withConfiguredRule(RemoveInterfacesRector::class, [
        ConfigurableRuleInterface::class,
    ])
    ->withSkip(['*/Tests/*', '*/tests/*', __DIR__ . '/../../tests']);
