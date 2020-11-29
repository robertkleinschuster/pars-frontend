<?php
return [
    'templates' => [
        'paths' => [
            'error' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'templates', 'error']),
            'layout' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'templates', 'layout']),
            'cmspage' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'templates', 'cmspage']),
            'cmsparagraph' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'templates', 'cmsparagraph']),
            'cmsmenu' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'templates', 'cmsmenu']),
            'cmspost' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'templates', 'cmspost']),
            ],
    ],
];
