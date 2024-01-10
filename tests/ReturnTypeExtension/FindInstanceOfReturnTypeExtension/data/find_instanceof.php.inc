<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

$nodeFinder = new \PhpParser\NodeFinder();
$string = $nodeFinder->findInstanceOf([], \PhpParser\Node\Scalar\String_::class);

assertType('array<PhpParser\Node\Scalar\String_>', $string);
