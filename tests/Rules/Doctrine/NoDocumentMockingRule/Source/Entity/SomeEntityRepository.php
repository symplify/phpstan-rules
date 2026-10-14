<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoDocumentMockingRule\Source\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<SomeEntity>
 */
final class SomeEntityRepository extends EntityRepository
{
}
