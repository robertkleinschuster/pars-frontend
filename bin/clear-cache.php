<?php
chdir(dirname(__DIR__));
require 'vendor/autoload.php';

/**
 * Self-called anonymous function that creates its own scope and keeps the global namespace clean.
 */
(function () {

    /** @var \Psr\Container\ContainerInterface $container */
    $container = require __DIR__ . '/../config/container.php';
    $adapter = $container->get(\Laminas\Db\Adapter\AdapterInterface::class);
    $translator = $container->get(\Laminas\I18n\Translator\TranslatorInterface::class);

    $cache = new \Pars\Core\Deployment\Cache($container->get('config'), $adapter);
    $cache->setTranslator($translator);
    $cache->clear();
    echo 'Cache Cleared';
})();
