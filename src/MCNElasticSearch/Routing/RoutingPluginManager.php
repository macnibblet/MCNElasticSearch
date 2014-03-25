<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\Routing;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class RoutingPluginManager
 *
 * @method \MCNElasticSearch\Routing\RoutingStrategyInterface get($name, $options = array(), $usePeeringServiceManagers = true)
 */
class RoutingPluginManager extends AbstractPluginManager
{
    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param mixed $plugin
     *
     * @throws Exception\InvalidRoutingStrategyException
     *
     * @return void
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof RoutingStrategyInterface) {
            return;
        }

        throw new Exception\InvalidRoutingStrategyException(
            'Plugin of type %s is invalid; must implement %s\RoutingStrategyInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        );
    }
}
