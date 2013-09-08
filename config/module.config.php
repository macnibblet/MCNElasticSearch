<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

use MCNElasticSearch\Service\DocumentService;
use MCNElasticSearch\Service\MappingService;
use MCNElasticSearch\Service\SearchService;

return [
    'MCNElasticSearch' => [

        /**
         * Client configuration
         */
        'client' => [],

        /**
         * Metadata configuration
         */
        'metadata' => [

            /**
             * List of object mappings
             */
            'objects' => [],

            /**
             * List of types E.g "SQL Tables"
             */
            'types' => []
        ],

        DocumentService::class => [
            'listeners' => []
        ],

        SearchService::class => [
            'listeners' => []
        ],

        MappingService::class => [
            'listeners' => []
        ]
    ],

    'console'         => ['router' => ['routes' => include __DIR__ . '/routes.config.php']],
    'service_manager' => include __DIR__ . '/service.config.php',
    'controllers'     => include __DIR__ . '/controller.config.php'
];
