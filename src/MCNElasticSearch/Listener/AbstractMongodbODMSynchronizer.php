<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Listener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;

/**
 * Class AbstractMongodbODMSynchronizer
 *
 * Provides a very simple API to help synchronize documents between MongoDB and ElasticSearch.
 */
abstract class AbstractMongodbODMSynchronizer extends AbstractSynchronizer
{
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
            Events::postRemove,
        ];
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event)
    {
        $entity = $event->getDocument();
        if ($this->isValid($entity)) {
            $this->service->update($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        $entity = $event->getDocument();
        if ($this->isValid($entity)) {
            $this->service->add($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postRemove(LifecycleEventArgs $event)
    {
        $entity = $event->getDocument();
        if ($this->isValid($entity)) {
            $this->service->delete($entity);
        }
    }
}
