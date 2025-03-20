<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoListenerWithoutContractRule\Fixture;

final class SkipAnotherSecurityListener extends
    \Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener
{

}
