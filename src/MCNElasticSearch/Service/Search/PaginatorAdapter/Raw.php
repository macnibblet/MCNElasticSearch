<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service\Search\PaginatorAdapter;

use Elastica\Result;

/**
 * Class Raw
 */
class Raw extends AbstractAdapter
{
    /**
     * @param Result $object
     * @return mixed
     */
    public function hydrate(Result $object)
    {
        return $object->getHit();
    }
}
