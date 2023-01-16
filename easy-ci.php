<?php

declare(strict_types=1);

use Symplify\EasyCI\Config\EasyCIConfig;

return static function (EasyCIConfig $easyCIConfig): void {
    $easyCIConfig->typesToSkip([
        \Symplify\PHPStanRules\Exception\DeprecatedException::class,
        \Symplify\PHPStanRules\NodeAnalyzer\AttributeFinder::class,
        \Symplify\PHPStanRules\NodeVisitor\AssignedToPropertyNodeVisitor::class,
    ]);
};
