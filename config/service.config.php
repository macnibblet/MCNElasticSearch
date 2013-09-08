<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

use Elastica\Client;
use MCNElasticSearch\Service\DocumentService;
use MCNElasticSearch\Service\MappingService;
use MCNElasticSearch\Service\MetadataService;
use MCNElasticSearch\Service\SearchService;
use MCNElasticSearch\ServiceFactory\ClientFactory;
use MCNElasticSearch\ServiceFactory\MappingServiceFactory;
use MCNElasticSearch\ServiceFactory\DocumentServiceFactory;
use MCNElasticSearch\ServiceFactory\MetadataServiceFactory;
use MCNElasticSearch\ServiceFactory\SearchServiceFactory;

/**
 * Service manager configuration for elastic search
 */
return [
    'factories' => [
        Client::class          => ClientFactory::class,
        SearchService::class   => SearchServiceFactory::class,
        MappingService::class  => MappingServiceFactory::class,
        DocumentService::class => DocumentServiceFactory::class,
        MetadataService::class => MetadataServiceFactory::class
    ]
];
