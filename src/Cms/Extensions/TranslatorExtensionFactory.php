<?php

namespace Pars\Frontend\Cms\Extensions;

use Laminas\I18n\Translator\TranslatorInterface;
use Psr\Container\ContainerInterface;

class TranslatorExtensionFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new TranslatorExtension($container->get(TranslatorInterface::class));
    }
}
