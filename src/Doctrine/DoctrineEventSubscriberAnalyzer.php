<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Doctrine;

use PhpParser\Node\Stmt\Class_;
use Symplify\PHPStanRules\Enum\DoctrineEvents;

final class DoctrineEventSubscriberAnalyzer
{
    public static function detect(Class_ $class): bool
    {
        // skip doctrine, as this is handling symfony only
        foreach ($class->getMethods() as $classMethod) {
            if (in_array($classMethod->name->toString(), DoctrineEvents::ORM_LIST)) {
                return true;
            }

            if (in_array($classMethod->name->toString(), DoctrineEvents::ODM_LIST)) {
                return true;
            }
        }

        return false;
    }
}
