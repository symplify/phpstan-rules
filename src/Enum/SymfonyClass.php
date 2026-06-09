<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class SymfonyClass
{
    /**
     * @var string
     */
    public const SECURITY_LISTENER = 'Symfony\Component\Security\Http\Firewall\AbstractListener';

    /**
     * @var string
     */
    public const FORM_SECURITY_LISTENER = 'Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener';

    /**
     * @var string
     */
    public const ROUTE_ATTRIBUTE = 'Symfony\Component\Routing\Attribute\Route';

    /**
     * @var string
     */
    public const ROUTE_ANNOTATION = 'Symfony\Component\Routing\Annotation\Route';

    /**
     * @var string
     */
    public const CONTROLLER = 'Symfony\Bundle\FrameworkBundle\Controller\Controller';

    /**
     * @var string
     */
    public const REQUIRED_ATTRIBUTE = 'Symfony\Contracts\Service\Attribute\Required';

    /**
     * @var string
     */
    public const ABSTRACT_CONTROLLER = 'Symfony\Bundle\FrameworkBundle\Controller\AbstractController';

    /**
     * @var string
     */
    public const EVENT_LISTENER_ATTRIBUTE = 'Symfony\Component\EventDispatcher\Attribute\AsEventListener';

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

    /**
     * @var string
     */
    public const COMMAND = 'Symfony\Component\Console\Command\Command';

    /**
     * @var string
     */
    public const VALIDATOR_TEST_CASE = 'Symfony\Component\Validator\Test\ConstraintValidatorTestCase';

    /**
     * @var string
     */
    public const CONTAINER_CONFIGURATOR = 'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator';

    /**
     * @var string
     */
    public const IS_GRANTED = 'Symfony\Component\Security\Http\Attribute\IsGranted';

    /**
     * @var string
     */
    public const ATTRIBUTE = 'Symfony\Component\DependencyInjection\Attribute\Autowire';

    /**
     * @var string
     */
    public const REQUEST = 'Symfony\Component\HttpFoundation\Request';
}
