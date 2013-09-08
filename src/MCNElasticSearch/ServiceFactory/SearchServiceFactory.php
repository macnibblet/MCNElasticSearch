<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\ServiceFactory;

use Elastica\Client;
use MCNElasticSearch\Service\MetadataService;
use MCNElasticSearch\Service\SearchService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SearchServiceFactory
 */
class SearchServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SearchService(
            $serviceLocator->get(Client::class),
            $serviceLocator->get(MetadataService::class),
            $serviceLocator->get('objectManager')
        );
    }
}
