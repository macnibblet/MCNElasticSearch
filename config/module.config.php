<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */
return [
    'MCNElasticSearch' => [

        /**
         * Client configuration
         */
        'connection' => [],

        /**
         * List of object mappings
         */
        'object_metadata' => [],

        /**
         * List of types E.g "SQL Tables"
         */
        'types' => []
    ],

    /**
     *
     */
    'log' => [
        'es.log' => [
            'writers' => [
                [
                    'name'    => 'stream',
                    'options' => [
                        'mode'   => 'a+',
                        'stream' => 'data/logs/elastic-search.log'
                    ]
                ]
            ]
        ]
    ],

    'console'         => ['router' => ['routes' => include __DIR__ . '/routes.config.php']],
    'service_manager' => include __DIR__ . '/service.config.php',
    'controllers'     => include __DIR__ . '/controller.config.php'
];
