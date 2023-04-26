<?php

declare(strict_types=1);

namespace Rules\Complexity\ForbiddenInlineClassMethodRule\Fixture\BuilderCall;

class ExampleBuilder
{
    public function firstMethod(): self
    {
        return $this;
    }

    public function secondMethod(): self
    {
        return $this;
    }

    public function thirdMethod(): self
    {
        return $this;
    }
}
