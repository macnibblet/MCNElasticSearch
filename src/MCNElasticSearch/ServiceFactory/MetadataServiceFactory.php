<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\ServiceFactory;

use Elastica\Client;
use MCNElasticSearch\Service\MetadataService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ClientServiceFactory
 */
class MetadataServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $sl
     *
     * @return \Elastica\Client
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        return new MetadataService($sl->get('Config')['MCNElasticSearch']['metadata']);
    }
}
