<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class SymfonyFunctionName
{
    /**
     * @var string
     */
    public const REF = 'Symfony\Component\DependencyInjection\Loader\Configurator\ref';

    /**
     * @var string
     */
    public const SERVICE = 'Symfony\Component\DependencyInjection\Loader\Configurator\service';

    /**
     * @var string
     */
    public const PARAM = 'Symfony\Component\DependencyInjection\Loader\Configurator\param';
}
