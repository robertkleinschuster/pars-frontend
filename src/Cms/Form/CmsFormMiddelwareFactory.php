<?php

namespace Pars\Frontend\Cms\Form;

use Laminas\Db\Adapter\AdapterInterface;
use Pars\Frontend\Cms\Helper\Config;
use Psr\Container\ContainerInterface;

/**
 * Class CmsFormMiddelwareFactory
 * @package Pars\Frontend\Cms\Form
 */
class CmsFormMiddelwareFactory
{

    /**
     * @param ContainerInterface $container
     * @return CmsFormMiddelware
     */
    public function __invoke(ContainerInterface $container)
    {
        return new CmsFormMiddelware(new Config($container->get(AdapterInterface::class)));
    }
}
