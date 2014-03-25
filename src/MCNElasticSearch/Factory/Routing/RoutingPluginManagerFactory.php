<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\Factory\Routing;

use MCNElasticSearch\Routing\RoutingPluginManager;
use MCNElasticSearch\Factory\Exception;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RoutingPluginManagerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @throws Exception\ConfigurationException
     *
     * @return RoutingPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (! isset($config['MCNElasticSearch']['routing_manager'])) {
            throw new Exception\ConfigurationException(
                'Could not found the configuration key "routing_manager" in MCNElasticSearch'
            );
        }

        return new RoutingPluginManager(
            new Config($config['MCNElasticSearch']['routing_manager'])
        );
    }
}
