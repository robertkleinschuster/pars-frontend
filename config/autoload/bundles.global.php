<?php
$hash = md5(random_bytes(5));
return [
    'bundles' => [
        'hash' => $hash,
        'list' => [
            [
                'type' => 'js',
                'output' => "script-insertion_$hash.js",
                'critical' => true,
                'sources' => [
                    __DIR__ . '/../../bundles/js/script-insertion.js',
                ]
            ],
            [
                'type' => 'js',
                'output' => "frontend-minimal_$hash.js",
                'critical' => true,
                'sources' => [
                    __DIR__ . '/../../bundles/js/01-jquery.min.js',
                    __DIR__ . '/../../bundles/js/loading-attribute-polyfill.min.js',
                ]
            ],
            [
                'type' => 'js',
                'output' => "frontend-bundle_$hash.js",
                'critical' => false,
                'sources' => [
                    __DIR__ . '/../../bundles/js/01-jquery.min.js',
                    __DIR__ . '/../../bundles/js/02-bootstrap.min.js',
                    __DIR__ . '/../../bundles/js/04-popper.min.js',
                ]
            ],
            [
                'type' => 'css',
                'output' => "frontend-critical_$hash.css",
                'critical' => true,
                'sources' => [
                    __DIR__ . '/../../bundles/css/bootstrap-grid.min.css',
                    __DIR__ . '/../../bundles/css/bootstrap-reboot.min.css',
                    __DIR__ . '/../../bundles/css/globals.css',
                ]
            ],
            [
                'type' => 'css',
                'output' => "frontend-bundle_$hash.css",
                'critical' => false,
                'sources' => [
                    __DIR__ . '/../../bundles/css/bootstrap.min.css',
                    __DIR__ . '/../../bundles/css/globals.css',
                    __DIR__ . '/../../bundles/css/cms.css',
                    __DIR__ . '/../../bundles/css/frontend.css',
                ]
            ],
        ]
    ]
];
