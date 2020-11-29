<?php
declare(strict_types=1);

namespace Pars\Frontend;

use Psr\Container\ContainerInterface;

class ApplicationContainerFactory
{
    public function __invoke()
    {
        $config = $this->getApplicationConfig();
        $dependencies = $config['dependencies'];
        $dependencies['services']['config'] = $config;
        return new ApplicationContainer($dependencies);
    }

    protected function getApplicationConfig(): array
    {
        return require realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'config', 'config.php']));
    }
}
