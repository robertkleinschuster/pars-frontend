<?php

namespace Pars\Frontend\Cms\Middleware;

use Laminas\Db\Adapter\AdapterInterface;
use Pars\Frontend\Cms\Helper\Config;
use Psr\Container\ContainerInterface;

/**
 * Class FrontendConfigMiddlewareFactory
 * @package Pars\Frontend\Cms\Middleware
 */
class FrontendConfigMiddlewareFactory
{
    /**
     * @param ContainerInterface $container
     * @return FrontendConfigMiddleware
     */
    public function __invoke(ContainerInterface $container): FrontendConfigMiddleware
    {
        return new FrontendConfigMiddleware(
            new Config($container->get(AdapterInterface::class)),
            $container->get('config')
        );
    }
}
