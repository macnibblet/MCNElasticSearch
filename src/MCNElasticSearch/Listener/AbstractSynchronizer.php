<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Listener;

use Doctrine\Common\EventSubscriber;
use MCNElasticSearch\Service\DocumentServiceInterface;

/**
 * Class AbstractSynchronizer
 */
abstract class AbstractSynchronizer implements EventSubscriber
{
    /**
     * @var \MCNElasticSearch\Service\DocumentServiceInterface
     */
    protected $service;

    /**
     * @param DocumentServiceInterface $service
     */
    public function __construct(DocumentServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Check that an object is of the proper instance
     *
     * @param mixed $object
     *
     * @return bool
     */
    abstract public function isValid($object);
}
