<?php
return [
    'bundles' => [
        'list' => [
            [
                'type' => 'js',
                'output' => "critical.js",
                'sources' => [
                    __DIR__ . '/../../bundles/js/insertion.js',
                    __DIR__ . '/../../bundles/js/scroll-state.js',
                ]
            ],
            [
                'type' => 'js',
                'output' => "cms.js",
                'sources' => [
                    __DIR__ . '/../../bundles/js/jquery-3.5.1.slim.min.js',
                    __DIR__ . '/../../bundles/js/bootstrap.bundle.min.js',
                    __DIR__ . '/../../bundles/js/loading-attribute-polyfill.min.js',
                    __DIR__ . '/../../bundles/js/smoothscroll.min.js',
                    __DIR__ . '/../../bundles/js/submit.js',
                    __DIR__ . '/../../bundles/js/frontend.js',
                ]
            ],
            [
                'type' => 'scss',
                'import' => __DIR__ . '/../../bundles/scss/critical',
                'output' => "critical.css",
                'entrypoint' => __DIR__ . '/../../bundles/scss/critical/critical.scss',
            ],
            [
                'type' => 'scss',
                'import' => __DIR__ . '/../../bundles/scss/base',
                'output' => "base.css",
                'entrypoint' => __DIR__ . '/../../bundles/scss/base/base.scss',
            ],
            [
                'type' => 'scss',
                'import' => __DIR__ . '/../../bundles/scss/cms',
                'output' => "cms.css",
                'entrypoint' => __DIR__ . '/../../bundles/scss/cms/cms.scss',
            ],
            [
                'type' => 'scss',
                'import' => __DIR__ . '/../../bundles/scss/tesla',
                'output' => "tesla.css",
                'entrypoint' => __DIR__ . '/../../bundles/scss/tesla/tesla.scss',
            ],
            [
                'type' => 'scss',
                'import' => __DIR__ . '/../../bundles/scss/noscript',
                'output' => "noscript.css",
                'entrypoint' => __DIR__ . '/../../bundles/scss/noscript/noscript.scss',
            ],
        ]
    ]
];
