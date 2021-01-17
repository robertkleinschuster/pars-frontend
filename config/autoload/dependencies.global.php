<?php

declare(strict_types=1);


use Mezzio\Csrf\CsrfGuardFactoryInterface;
use Mezzio\Csrf\SessionCsrfGuardFactory;

return [
    // Provides application-wide services.
    // We recommend using fully-qualified class names whenever possible as
    // service names.
    'dependencies' => [
        // Use 'aliases' to alias a service name to another service. The
        // key is the alias name, the value is the service to which it points.
        'aliases' => [
            // Fully\Qualified\ClassOrInterfaceName::class => Fully\Qualified\ClassName::class,
            Mezzio\Application::class => Pars\Frontend\Application::class,
            Mezzio\Container\ApplicationFactory::class => Pars\Frontend\ApplicationFactory::class,
        ],
        // Use 'invokables' for constructor-less services, or services that do
        // not require arguments to the constructor. Map a service name to the
        // class name.
        'invokables' => [
            // Fully\Qualified\InterfaceName::class => Fully\Qualified\ClassName::class,
        ],
        // Use 'factories' for services provided by callbacks/factory classes.
        'factories' => [
            // Fully\Qualified\ClassName::class => Fully\Qualified\FactoryName::class,
            Pars\Frontend\Application::class => Pars\Frontend\ApplicationFactory::class,
            Pars\Frontend\ApplicationContainer::class => Pars\Frontend\ApplicationContainerFactory::class,
            \Pars\Frontend\Cms\Handler\CmsHandler::class => \Pars\Frontend\Base\Handler\FrontendHandlerFactory::class,
            \Pars\Frontend\Cms\Handler\BrowserconfigHandler::class => \Pars\Frontend\Base\Handler\FrontendHandlerFactory::class,
            \Pars\Frontend\Cms\Handler\RobotsHandler::class => \Pars\Frontend\Base\Handler\FrontendHandlerFactory::class,
            \Pars\Frontend\Cms\Handler\SitemapHandler::class => \Pars\Frontend\Base\Handler\FrontendHandlerFactory::class,
            \Pars\Frontend\Cms\Form\CmsFormMiddelware::class => \Pars\Frontend\Cms\Form\CmsFormMiddelware::class,
            \Mezzio\Middleware\ErrorResponseGenerator::class => \Pars\Frontend\Cms\Error\ErrorResponseGeneratorFactory::class,
            \Mezzio\Response\ServerRequestErrorResponseGenerator::class => \Pars\Frontend\Cms\Error\ServerRequestErrorResponseGeneratorFactory::class,
            \Mezzio\Handler\NotFoundHandler::class => \Pars\Frontend\Cms\Error\NotFoundHandlerFactory::class,
            \Pars\Frontend\Cms\Error\NotFoundHandler::class => \Pars\Frontend\Cms\Error\NotFoundHandlerFactory::class,
            \Pars\Frontend\Cms\Middleware\FrontendConfigMiddleware::class => \Pars\Frontend\Cms\Middleware\FrontendConfigMiddlewareFactory::class,
            \Pars\Frontend\Cms\Extensions\TranslatorExtension::class => \Pars\Frontend\Cms\Extensions\TranslatorExtensionFactory::class,
            \Pars\Frontend\Cms\Extensions\PlaceholderExtension::class => \Pars\Frontend\Cms\Extensions\PlaceholderExtensionFactory::class,
            \Pars\Frontend\Cms\Extensions\PathExtension::class => \Pars\Frontend\Cms\Extensions\PathExtensionFactory::class,
            \Laminas\HttpHandlerRunner\Emitter\EmitterInterface::class => \Pars\Core\Emitter\EmitterFactory::class,
        ],
    ],
];
