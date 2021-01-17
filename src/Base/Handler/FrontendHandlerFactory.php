<?php


namespace Pars\Frontend\Base\Handler;


use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Pars\Frontend\Cms\Handler\CmsHandler;
use Psr\Container\ContainerInterface;

class FrontendHandlerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        return new $requestedName($container->get(TemplateRendererInterface::class), $container->get(UrlHelper::class), $container->get('config'));
    }
}
