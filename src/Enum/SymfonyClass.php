<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class SymfonyClass
{
    /**
     * @var string
     */
    public const ROUTE_ATTRIBUTE = 'Symfony\Component\Routing\Attribute\Route';

    /**
     * @api
     * @var string
     */
    public const ROUTE_ANNOTATION = 'Symfony\Component\Routing\Annotation\Route';

    /**
     * @var string
     */
    public const SYMFONY_CONTROLLER = 'Symfony\Bundle\FrameworkBundle\Controller\Controller';

    /**
     * @var string
     */
    public const REQUIRED_ATTRIBUTE = 'Symfony\Contracts\Service\Attribute\Required';

    /**
     * @var string
     */
    public const SYMFONY_ABSTRACT_CONTROLLER = 'Symfony\Bundle\FrameworkBundle\Controller\AbstractController';

    /**
     * @var string
     */
    public const EVENT_SUBSCRIBER_INTERFACE = 'Symfony\Component\EventDispatcher\EventSubscriberInterface';

    /**
     * @var string
     */
    public const EVENT_DISPATCHER_INTERFACE = 'Symfony\Component\EventDispatcher\EventDispatcherInterface';

    /**
     * @var string
     */
    public const FORM_TYPE = 'Symfony\Component\Form\AbstractType';

    /**
     * @var string
     */
    public const ROUTE_IMPORT_CONFIGURATOR = 'Symfony\Component\Routing\Loader\Configurator\ImportConfigurator';

    /**
     * @var string
     */
    public const FORM_EVENTS = 'Symfony\Component\Form\FormEvents';

    /**
     * @var string
     */
    public const URL_GENERATOR = 'Symfony\Component\Routing\Generator\UrlGeneratorInterface';
}
