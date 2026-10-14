<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoPublicNonActionMethodInControllerRule\Fixture;

use Symfony\Component\Routing\Annotation\Route as AnnotationRoute;
use Symfony\Component\Routing\Attribute\Route;

final class SkipRouteAttributeController
{
    #[Route('/submit', name: 'submit', methods: ['POST'])]
    public function submit(): void
    {
    }

    #[AnnotationRoute('/legacy', name: 'legacy')]
    public function legacy(): void
    {
    }
}
