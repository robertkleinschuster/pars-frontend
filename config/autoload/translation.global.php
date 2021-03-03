<?php

return [
    'translator' => [
        'locale' => ['de_AT', 'en_US'],
        'translation_file_patterns' => [
            [
                'type' => Laminas\I18n\Translator\Loader\PhpArray::class,
                'base_dir' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'translation', 'frontend']),
                'pattern' => '%s.php',
                'text_domain' => 'frontend'
            ],
        ],
        'translation_files' => [
        ],
        'remote_translation' => [
            [
                'type' => \Laminas\I18n\Translator\Loader\RemoteLoaderInterface::class,
                'text_domain' => 'frontend'
            ],
        ],

        'cache' => [
            'adapter' => [
                'name' => Laminas\Cache\Storage\Adapter\Filesystem::class,
                'options' => [
                    'cache_dir' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'data', 'cache', 'translation']),
                ],
            ],
            'plugins' => [
                'serializer',
                'exception_handler' => [
                    'throw_exceptions' => false,
                ],
            ],
        ],
        'event_manager_enabled' => true
    ]
];
