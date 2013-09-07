<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

use MCNElasticSearch\ServiceFactory\MappingServiceFactory;
use MCNElasticSearch\ServiceFactory\DocumentServiceFactory;
use MCNElasticSearch\ServiceFactory\ConfigurationServiceFactory;
use MCNElasticSearch\ServiceFactory\SearchServiceFactory;

/**
 * Service manager configuration for elastic search
 */
return [
    'factories' => [
        'es.service.search'        => SearchServiceFactory::class,
        'es.service.mapping'       => MappingServiceFactory::class,
        'es.service.document'      => DocumentServiceFactory::class,
        'es.service.configuration' => ConfigurationServiceFactory::class
    ]
];
