<?php

namespace Pars\Frontend\Cms\Extensions;

use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

class PathExtensionFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new PathExtension($container->get(UrlHelper::class), $container->get(ServerUrlHelper::class));
    }
}
