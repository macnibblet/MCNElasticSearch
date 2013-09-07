<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use MCNElasticSearch\Service\DocumentServiceInterface;

/**
 * Class AbstractObjectSynchronizer
 *
 * A very simple abstract object synchronizer
 */
abstract class AbstractObjectSynchronizer implements EventSubscriber
{
    /**
     * @var \MCNElasticSearch\Service\DocumentServiceInterface
     */
    private $service;

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

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove
        ];
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        if ($this->isValid($entity)) {
            $this->service->update($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        if ($this->isValid($entity)) {
            $this->service->add($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postRemove(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        if ($this->isValid($entity)) {
            $this->service->delete($entity);
        }
    }
}


