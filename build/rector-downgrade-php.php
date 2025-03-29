<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withDowngradeSets(php74: true)
    ->withSkip(['*/Tests/*', '*/tests/*', __DIR__ . '/../tests']);
