<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReferenceRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoReferenceRule\Source\AbstractMagicFinderRepository;

final class MagicParentFinderReference extends AbstractMagicFinderRepository
{
    public function findByChannel(&$reference)
    {
    }
}
