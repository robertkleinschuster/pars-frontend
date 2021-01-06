<?php


namespace Pars\Frontend\Cms\Extensions;


use Laminas\I18n\Translator\TranslatorInterface;
use Psr\Container\ContainerInterface;

class PlaceholderExtensionFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new PlaceholderExtension($container->get(TranslatorInterface::class));
    }

}
