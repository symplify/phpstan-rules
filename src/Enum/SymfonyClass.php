<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class SymfonyClass
{
    public const ROUTE_ATTRIBUTE = 'Symfony\Component\Routing\Attribute\Route';

    /**
     * @api
     */
    public const ROUTE_ANNOTATION = 'Symfony\Component\Routing\Annotation\Route';

    public const CONTROLLER = 'Symfony\Bundle\FrameworkBundle\Controller\Controller';

    public const REQUIRED_ATTRIBUTE = 'Symfony\Contracts\Service\Attribute\Required';

    public const ABSTRACT_CONTROLLER = 'Symfony\Bundle\FrameworkBundle\Controller\AbstractController';

    public const EVENT_SUBSCRIBER_INTERFACE = 'Symfony\Component\EventDispatcher\EventSubscriberInterface';

    public const EVENT_DISPATCHER_INTERFACE = 'Symfony\Component\EventDispatcher\EventDispatcherInterface';

    public const FORM_TYPE = 'Symfony\Component\Form\AbstractType';

    public const ROUTE_IMPORT_CONFIGURATOR = 'Symfony\Component\Routing\Loader\Configurator\ImportConfigurator';

    public const FORM_EVENTS = 'Symfony\Component\Form\FormEvents';

    public const URL_GENERATOR = 'Symfony\Component\Routing\Generator\UrlGeneratorInterface';

    public const COMMAND = 'Symfony\Component\Console\Command\Command';
}
