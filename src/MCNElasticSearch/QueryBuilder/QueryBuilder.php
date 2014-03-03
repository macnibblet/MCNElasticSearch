<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder;

use MCNElasticSearch\QueryBuilder\Composite\CompositeInterface;
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

    protected function getFilterArray()
    {
        if ($this->filter instanceof FilterInterface) {
            list ($method, $body)  = $this->filter->toArray();

            return [$method => $body];
        } else {

            if ($this->filter instanceof CompositeInterface && $this->filter->isEmpty()) {
                return [];
            }

            return $this->filter->toArray();
        }
    }

    protected function getQueryArray()
    {
        if ($this->query instanceof QueryInterface) {
            list ($method, $body) = $this->query->toArray();

            return [$method => $body];
        } else {

            if ($this->query instanceof CompositeInterface && $this->query->isEmpty()) {
                return [];
            }

            return $this->query->toArray();
        }
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

            'from' => $this->from,
            'size' => $this->size
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
}
