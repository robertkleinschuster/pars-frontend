<?php


namespace Pars\Frontend\Cms\Middleware;


use Laminas\Db\Adapter\AdapterInterface;
use Pars\Frontend\Cms\Helper\Config;
use Psr\Container\ContainerInterface;

class FrontendConfigMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new FrontendConfigMiddleware(new Config($container->get(AdapterInterface::class)), $container->get('config'));
    }

}
