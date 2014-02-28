<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Container;

use MCNElasticSearch\QueryBuilder\Query\QueryInterface;

class QueryContainer
{
    /**
     * @var array
     */
    protected $queries = [];

    /**
     * @param QueryInterface $query
     */
    public function add(QueryInterface $query)
    {
        $this->queries[] = $query;
    }

    public function toArray()
    {
        $result = [];

        foreach ($this->queries as $query) {
            list ($method, $body) = $query->toArray();

            if (! isset($result[$method])) {
                $result[$method] = [];
            }

            $result[$method] = array_merge($result[$method], $body);
        }

        return $result;
    }
}
