<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service;

use Elastica\Query;
use Zend\EventManager\EventsCapableInterface;

/**
 * Interface SearchServiceInterface
 */
interface SearchServiceInterface extends EventsCapableInterface
{
    const HYDRATE_RAW             = 'raw';
    const HYDRATE_DOCTRINE_OBJECT = 'objectManager';

    /**
     * Perform a search
     *
     * @param string          $objectClassName
     * @param \Elastica\Query $query
     * @param string          $hydration
     *
     * @return \Zend\Paginator\Paginator
     */
    public function search($objectClassName, Query $query, $hydration = self::HYDRATE_RAW);
}
