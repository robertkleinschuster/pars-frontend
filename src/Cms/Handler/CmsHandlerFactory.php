<?php

namespace Pars\Frontend\Cms\Handler;

use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class CmsHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new CmsHandler($container->get(TemplateRendererInterface::class), $container->get(UrlHelper::class)
        ,$container->get('config')
        );
    }
}
