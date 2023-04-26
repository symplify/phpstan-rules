<?php

declare(strict_types=1);

namespace Rules\Complexity\ForbiddenInlineClassMethodRule\Fixture\BuilderCall;

final class SkipBuilderCall
{
    private ExampleBuilder $exampleBuilder;

    public function run()
    {
        $this->exampleBuilder = new ExampleBuilder();

        return $this->away();
    }

    private function away()
    {
        return $this->exampleBuilder
            ->firstMethod()
            ->secondMethod()
            ->thirdMethod();
    }
}
