<?php

declare(strict_types=1);

namespace Pars\Frontend;

/**
 * The configuration provider for the Frontend module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke(): array
    {
        return [

            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                \Pars\Frontend\Application::class => \Pars\Frontend\ApplicationFactory::class,
                \Pars\Frontend\ApplicationContainer::class => \Pars\Frontend\ApplicationContainerFactory::class,
            ],
        ];
    }
}
