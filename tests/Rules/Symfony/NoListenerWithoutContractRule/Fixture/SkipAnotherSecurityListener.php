<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoListenerWithoutContractRule\Fixture;

use Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener;

final class SkipAnotherSecurityListener extends UsernamePasswordFormAuthenticationListener
{
}
