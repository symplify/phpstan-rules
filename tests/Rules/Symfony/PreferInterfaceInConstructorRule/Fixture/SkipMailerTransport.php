<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\PreferInterfaceInConstructorRule\Fixture;

use Symfony\Component\Mailer\Transport;

final class SkipMailerTransport
{
    public function __construct(
        private Transport $transport,
    ) {
    }
}
