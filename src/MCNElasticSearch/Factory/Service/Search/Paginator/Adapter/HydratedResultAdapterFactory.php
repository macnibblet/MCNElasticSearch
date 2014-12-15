<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\Factory\Service\Search\Paginator\Adapter;

use MCNElasticSearch\Factory\Exception;
use MCNElasticSearch\Service\Search\Paginator\Adapter\HydratedResultAdapter;
use MCNElasticSearch\Service\Search\Paginator\AdapterPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HydratedResultAdapterFactory implements FactoryInterface, MutableCreationOptionsInterface
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * Set creation options
     *
     * @param  array $options
     *
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Create service
     *
     * @param AdapterPluginManager|ServiceLocatorInterface $adapterManager
     *
     * @throws Exception\InvalidArgumentException
     *
     * @return HydratedResultAdapter
     */
    public function createService(ServiceLocatorInterface $adapterManager)
    {
        if (! isset($this->options['hydrator'])) {
            throw new Exception\InvalidArgumentException('No hydrator has been configured.');
        }

        if (! isset($this->options['prototype'])) {
            throw new Exception\InvalidArgumentException('No prototype has been configured.');
        }

        $sl = $adapterManager->getServiceLocator();

        $hydrator  = $sl->get('hydratorManager')->get($this->options['hydrator']);
        $prototype = $this->options['prototype'];

        return new HydratedResultAdapter($hydrator, $prototype);
    }
}
