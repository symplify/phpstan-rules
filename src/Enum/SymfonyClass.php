<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class SymfonyClass
{
    public const string SECURITY_LISTENER = 'Symfony\Component\Security\Http\Firewall\AbstractListener';

    public const string FORM_SECURITY_LISTENER = 'Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener';

    public const string ROUTE_ATTRIBUTE = 'Symfony\Component\Routing\Attribute\Route';

    public const string ROUTE_ANNOTATION = 'Symfony\Component\Routing\Annotation\Route';

    public const string CONTROLLER = 'Symfony\Bundle\FrameworkBundle\Controller\Controller';

    public const string REQUIRED_ATTRIBUTE = 'Symfony\Contracts\Service\Attribute\Required';

    public const string ABSTRACT_CONTROLLER = 'Symfony\Bundle\FrameworkBundle\Controller\AbstractController';

    public const string EVENT_LISTENER_ATTRIBUTE = 'Symfony\Component\EventDispatcher\Attribute\AsEventListener';

    public const string EVENT_SUBSCRIBER_INTERFACE = 'Symfony\Component\EventDispatcher\EventSubscriberInterface';

    public const string EVENT_DISPATCHER_INTERFACE = 'Symfony\Component\EventDispatcher\EventDispatcherInterface';

    public const string FORM_TYPE = 'Symfony\Component\Form\AbstractType';

    public const string ROUTE_IMPORT_CONFIGURATOR = 'Symfony\Component\Routing\Loader\Configurator\ImportConfigurator';

    public const string FORM_EVENTS = 'Symfony\Component\Form\FormEvents';

    public const string URL_GENERATOR = 'Symfony\Component\Routing\Generator\UrlGeneratorInterface';

    public const string COMMAND = 'Symfony\Component\Console\Command\Command';

    public const string VALIDATOR_TEST_CASE = 'Symfony\Component\Validator\Test\ConstraintValidatorTestCase';

    public const string CONTAINER_CONFIGURATOR = 'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator';

    public const string IS_GRANTED = 'Symfony\Component\Security\Http\Attribute\IsGranted';

    public const string ATTRIBUTE = 'Symfony\Component\DependencyInjection\Attribute\Autowire';

    public const string REQUEST = 'Symfony\Component\HttpFoundation\Request';
}
