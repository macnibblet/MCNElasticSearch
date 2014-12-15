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

    /**
     * @var Container\ScriptFieldContainer
     */
    protected $scriptFields;

    /**
     * @var mixed
     */
    protected $source = false;

    /**
     * @var Container\SortContainer
     */
    protected $sort;

    /**
     * @var HasParent
     */
    protected $hasParent;

    public function __construct()
    {
        $this->sort         = new Container\SortContainer();
        $this->scriptFields = new Container\ScriptFieldContainer();
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
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return HasParent
     */
    public function getHasParent()
    {
        return $this->hasParent;
    }

    /**
     * @param HasParent $hasParent
     */
    public function setHasParent(HasParent $hasParent)
    {
        $this->hasParent = $hasParent;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function getScriptFields()
    {
        return $this->scriptFields;
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
            return false;
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

    protected function getSortArray()
    {
        if ($this->sort->isEmpty()) {
            return false;
        }

        return iterator_to_array($this->sort->toArray());
    }

    protected function getScriptFieldsArray()
    {
        if ($this->scriptFields->isEmpty()) {
            return false;
        }

        return iterator_to_array($this->scriptFields->toArray());
    }

    protected function getHasParentArray()
    {
        if (! $this->hasParent || $this->hasParent->isEmpty()) {
            return false;
        }

        return $this->hasParent->toArray();
    }

    public function toArray()
    {
        return array_filter([
            'query' => [
                'filtered' => [
                    'filter' => $this->getFilterArray(),
                    'query'  => $this->getQueryArray()
                ],

            ],

            'facets' => $this->getFacetArray(),
            'sort'   => $this->getSortArray(),

            '_source'       => $this->source,
            'script_fields' => $this->getScriptFieldsArray(),

            'from' => $this->from,
            'size' => $this->size
        ]);
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
