<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Container;

use MCNElasticSearch\QueryBuilder\Composite\CompositeInterface;
use MCNElasticSearch\QueryBuilder\Facet\FacetInterface;

class FacetContainer implements CompositeInterface
{
    /**
     * @var FacetInterface[]
     */
    protected $facets = [];

    public function add(FacetInterface $facet)
    {
        $this->facets[] = $facet;
    }

    public function toArray()
    {
        $result = [];
        foreach ($this->facets as $facet) {
            list ($method, $body) = $facet->toArray();

            $result[$facet->getName()][$method] = $body;
        }

        return $result;
    }

    public function isEmpty()
    {
        return empty($this->facets);
    }
}
