<?php
return [
    'translator' => [
        'locale' => ['de_AT'],
        'translation_file_patterns' => [
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
