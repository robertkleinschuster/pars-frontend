<?php

namespace Pars\Frontend\Base\Handler;

use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

/**
 * Class FrontendHandlerFactory
 * @package Pars\Frontend\Base\Handler
 */
class FrontendHandlerFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        return new $requestedName(
            $container->get(TemplateRendererInterface::class),
            $container->get(UrlHelper::class),
            $container->get('config')
        );
    }
}
