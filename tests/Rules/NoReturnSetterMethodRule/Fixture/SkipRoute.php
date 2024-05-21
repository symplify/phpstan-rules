<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnSetterMethodRule\Fixture;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class SkipRoute
{
    #[Route('some_route')]
    public function setIncome(string $name): JsonResponse
    {
        return new JsonResponse();
    }
}
