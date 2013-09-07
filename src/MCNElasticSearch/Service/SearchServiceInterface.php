<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service;

use Elastica\Query;

/**
 * Interface SearchServiceInterface
 */
interface SearchServiceInterface
{
    const HYDRATE_RAW             = 'raw';
    const HYDRATE_ARRAY           = 'array';
    const HYDRATE_OBJECT          = 'object';
    const HYDRATE_DOCTRINE_OBJECT = 'objectManager';

    /**
     * Perform a search
     *
     * @param string          $objectClassName
     * @param \Elastica\Query $criteria
     * @param string          $hydration
     *
     * @return \Zend\Paginator\Paginator
     */
    public function search($objectClassName, Query $criteria, $hydration = self::HYDRATE_OBJECT);
}
