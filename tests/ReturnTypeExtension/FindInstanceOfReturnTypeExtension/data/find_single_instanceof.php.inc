<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

$nodeFinder = new \PhpParser\NodeFinder();
$string = $nodeFinder->findFirstInstanceOf([], \PhpParser\Node\Scalar\String_::class);

assertType('PhpParser\Node\Scalar\String_|null', $string);
