<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder;

class QueryBuilder
{
    /**
     * @var Container\QueryContainer
     */
    protected $query;

    /**
     * @var Container\FilterContainer
     */
    protected $filter;

    public function __construct()
    {
        $this->query  = new Container\QueryContainer();
        $this->filter = new Container\FilterContainer();
        $this->sort   = new Container\SortContainer();
    }

    public function filter()
    {
        return $this->filter;
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function query()
    {
        return $this->query;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function sort()
    {
        return $this->sort;
    }

    public function toArray()
    {
        return [
            'query' => [
                'filtered' => [
                    'filter' => $this->filter->toArray(),
                    'query'  => $this->query->toArray()
                ],
            ]
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
}
