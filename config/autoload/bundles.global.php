<?php
$hash = md5(random_bytes(5));
return [
    'bundles' => [
        'hash' => $hash,
        'list' => [
            [
                'type' => 'js',
                'output' => "critical_$hash.js",
                'unlink' => "critical_*",
                'critical' => true,
                'sources' => [
                    __DIR__ . '/../../bundles/js/insertion.js',
                    __DIR__ . '/../../bundles/js/scroll-state.js',
                ]
            ],
            [
                'type' => 'js',
                'output' => "frontend-minimal_$hash.js",
                'unlink' => "frontend-minimal_*.js",
                'critical' => true,
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
                'type' => 'css',
                'output' => "frontend-critical_$hash.css",
                'unlink' => "frontend-critical_*.css",
                'critical' => true,
                'sources' => [
                    __DIR__ . '/../../bundles/css/bootstrap-reboot.min.css',
                    __DIR__ . '/../../bundles/css/bootstrap-grid.min.css',
                    __DIR__ . '/../../bundles/css/globals.css',
                ]
            ],
            [
                'type' => 'css',
                'output' => "frontend-bundle_$hash.css",
                'unlink' => "frontend-bundle_*.css",
                'critical' => false,
                'sources' => [
                    __DIR__ . '/../../bundles/css/bootstrap.min.css',
                    __DIR__ . '/../../bundles/css/globals.css',
                    __DIR__ . '/../../bundles/css/cms.css',
                    __DIR__ . '/../../bundles/css/frontend.css',
                    __DIR__ . '/../../bundles/css/tesla.css',
                ]
            ],
        ]
    ]
];
