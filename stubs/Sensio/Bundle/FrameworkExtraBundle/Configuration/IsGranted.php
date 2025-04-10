<?php

namespace Sensio\Bundle\FrameworkExtraBundle\Configuration;

#[\Attribute]
class IsGranted
{
    public function __construct(string $resource)
    {
    }
}
