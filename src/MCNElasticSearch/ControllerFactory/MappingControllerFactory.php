<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\ControllerFactory;

use MCNElasticSearch\Controller\MappingController;
use MCNElasticSearch\Service\MappingService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class MappingControllerFactory
 */
class MappingControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface|\Zend\Mvc\Controller\ControllerManager $controllerManager
     * @return MappingController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        return new MappingController(
            $controllerManager->getServiceLocator()->get(MappingService::class)
        );
    }
}
