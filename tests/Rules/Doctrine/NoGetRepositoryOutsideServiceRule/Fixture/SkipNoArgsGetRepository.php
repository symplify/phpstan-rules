<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOutsideServiceRule\Fixture;

final class SkipNoArgsGetRepository
{
    public function run(object $someService): void
    {
        $someRepository = $someService->getRepository();
    }
}
