<?php
return [
    'plates' => [
        'extensions' => [
            \Pars\Frontend\Cms\Extensions\CmsContentExtension::class,
            \Pars\Frontend\Cms\Extensions\TranslatorExtension::class,
            \Pars\Frontend\Cms\Extensions\PlaceholderExtension::class,
            \Pars\Frontend\Cms\Extensions\PathExtension::class,
            \Pars\Frontend\Cms\Extensions\OpenGraphExtension::class,
            \Pars\Frontend\Cms\Extensions\ImagePathExtension::class,
            \Pars\Frontend\Cms\Extensions\StylesheetsExtension::class,
            \Pars\Frontend\Cms\Extensions\JavascriptExtension::class,
            \Pars\Frontend\Cms\Extensions\FormExtension::class
        ]
    ],
    'templates' => [
        'paths' => [
            'index' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'templates', 'index']),
            'error' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'templates', 'error']),
            'layout' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'templates', 'layout']),
            'cmspage' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'templates', 'cmspage']),
            'cmsparagraph' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'templates', 'cmsparagraph']),
            'cmsmenu' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'templates', 'cmsmenu']),
            'cmspost' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'templates', 'cmspost']),
            'file' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'templates', 'file']),
            'meta' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'templates', 'meta']),
        ],
    ],
];
