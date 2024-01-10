<?php

declare(strict_types=1);

use PhpParser\Node\Scalar\String_;
use Rector\NodeTypeResolver\Node\AttributeKey;

$node = new String_('value');

$scope = $node->getAttribute(AttributeKey::SCOPE);
\PHPStan\Testing\assertType('PHPStan\Analyser\Scope|null', $scope);
