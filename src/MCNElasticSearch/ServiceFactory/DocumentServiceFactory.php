<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\ServiceFactory;

use MCNElasticSearch\Service\DocumentService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DocumentServiceFactory
 */
class DocumentServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new DocumentService(
            $serviceLocator->get('es.service.configuration'),
            $serviceLocator->get('hydratorManager'),
            $serviceLocator->get('es.log')
        );
    }
}
