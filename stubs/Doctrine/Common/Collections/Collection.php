<?php

namespace Doctrine\Common\Collections;

if (interface_exists('Doctrine\Common\Collections\Collection')) {
    return;
}

/**
 * @template TKey of array-key
 * @template T
 */
interface Collection extends \Countable, \IteratorAggregate, \ArrayAccess
{
}
