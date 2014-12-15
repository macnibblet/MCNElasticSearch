<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\Service\Search\Paginator;

use MCNElasticSearch\Service\Search\Paginator\Adapter\AbstractAdapter;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class AdapterPluginManager
 *
 * @method AbstractAdapter get($name, $options = [], $usePeeringServiceManagers = true)
 */
class AdapterPluginManager extends AbstractPluginManager
{
    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param mixed $plugin
     *
     * @throws Exception\InvalidAdapterException if invalid
     *
     * @return void
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Adapter\AbstractAdapter) {
            return;
        }

        throw new Exception\InvalidAdapterException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Adapter\AbstractAdapter',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
