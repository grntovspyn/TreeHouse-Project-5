<?php
return [
    'settings' => [
        'displayErrorDetails' => true,

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],

        // Twig view settings
        'view' => [
            'template_path' => __DIR__ . '/../templates/',
            'cache' => false,
            'auto_reload' => true,
          ],

       
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true,
        'db' => [
            'driver' => 'sqlite',
            'host' => 'localhost',
            'database' => __DIR__ . '/../src/journal.db',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'foreign_key_constraints' => true,
        ]
    
    ],
];
