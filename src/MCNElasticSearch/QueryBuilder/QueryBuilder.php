<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder;

use MCNElasticSearch\QueryBuilder\Composite\CompositeInterface;
use MCNElasticSearch\QueryBuilder\Facet\FacetInterface;
use MCNElasticSearch\QueryBuilder\Filter\FilterInterface;
use MCNElasticSearch\QueryBuilder\Query\QueryInterface;

class QueryBuilder
{
    /**
     * @var array|null
     */
    protected $fields = null;

    /**
     * @var integer
     */
    protected $from = 0;

    /**
     * @var integer
     */
    protected $size = 25;

    /**
     * @var QueryInterface
     */
    protected $query;

    /**
     * @var FilterInterface
     */
    protected $filter;

    /**
     * @var FacetInterface
     */
    protected $facet;

    public function __construct()
    {
        $this->sort = new Container\SortContainer();
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function setFacet($facet)
    {
        $this->facet = $facet;
    }

    public function sort()
    {
        return $this->sort;
    }

    /**
     * @param int $from
     */
    public function setFrom($from)
    {
        $this->from = (int) $from;
    }

    /**
     * @return int
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = (int) $size;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return array|null
     */
    public function getFields()
    {
        return $this->fields;
    }

    protected function getFilterArray()
    {
        if ($this->filter instanceof FilterInterface) {
            list ($method, $body)  = $this->filter->toArray();

            return [$method => $body];
        }

        if ($this->filter instanceof CompositeInterface && $this->filter->isEmpty()) {
            return [];
        }

        return $this->filter->toArray();
    }

    protected function getQueryArray()
    {
        if ($this->query instanceof QueryInterface) {
            list ($method, $body) = $this->query->toArray();

            return [$method => $body];
        }

        if ($this->query instanceof CompositeInterface && $this->query->isEmpty()) {
            return [];
        }

        return $this->query->toArray();
    }

    protected function getFacetArray()
    {
        if ($this->facet === null) {
            return [];
        }

        if ($this->facet instanceof FacetInterface) {
            list ($method, $body) = $this->facet->toArray();

            return [$method => $body];
        }

        if ($this->facet instanceof CompositeInterface && $this->facet->isEmpty()) {
            return [];
        }

        return $this->facet->toArray();
    }

    public function toArray()
    {
        return [
            'query' => [
                'filtered' => [
                    'filter' => $this->getFilterArray(),
                    'query'  => $this->getQueryArray()
                ],
            ],

            'fields' => $this->fields,
            'facets' => $this->getFacetArray(),

            'from' => $this->from,
            'size' => $this->size
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public function __toString()
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
}
