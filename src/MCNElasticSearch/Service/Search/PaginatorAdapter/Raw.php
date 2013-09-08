<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service\Search\PaginatorAdapter;

/**
 * Class Raw
 */
class Raw extends AbstractAdapter
{
    /**
     * Returns an collection of items for a page.
     *
     * @param  int $offset Page offset
     * @param  int $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->query->setFrom($offset);
        $this->query->setSize($itemCountPerPage);

        return $this->searchable->search($this->query)->getResults();
    }
}
