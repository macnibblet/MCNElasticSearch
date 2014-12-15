<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\Service\Search\Paginator\Adapter;

use Zend\Stdlib\Hydrator\HydratorInterface;

class HydratedResultAdapter extends AbstractAdapter
{
    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var object
     */
    protected $prototype;

    /**
     * @param HydratorInterface $hydrator
     * @param object            $prototype
     */
    public function __construct(HydratorInterface $hydrator, $prototype)
    {
        $this->hydrator  = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }

    /**
     * @return object
     */
    public function getPrototype()
    {
        return $this->prototype;
    }

    /**
     * Hydrate a cloned prototype with the array result
     *
     * @param array $result
     *
     * @return object
     */
    protected function hydrate(array $result)
    {
        return $this->hydrator->hydrate($result['_source'], clone $this->prototype);
    }

    /**
     * Returns a collection of items for a page.
     *
     * @param  int $offset           Page offset
     * @param  int $itemCountPerPage Number of items per page
     *
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        if ($this->count() == 0) {
            return [];
        }

        $this->query['from'] = $offset;
        $this->query['size'] = $itemCountPerPage;

        $result = $this->client->search($this->query);
        $return = [];

        foreach ($result['hits']['hits'] as $result) {
            $return[$result['_id']] = $this->hydrate($result);
        }

        return $return;
    }
}
