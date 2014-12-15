<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Filter;

use MCNElasticSearch\QueryBuilder\Composite\CompositeInterface;
use MCNElasticSearch\QueryBuilder\ExpressionInterface;

class OrFilter implements FilterInterface
{
    protected $items = [];

    public function add(FilterInterface $filter)
    {
        $this->items[] = $filter;
    }

    public function toArray()
    {
        $callback = function (ExpressionInterface $filter) {
            if ($filter instanceof CompositeInterface && $filter->isEmpty()) {
                return false;
            }

            list ($method, $body) = $filter->toArray();

            return [$method => $body];
        };

        return ['or',  array_values(array_filter(array_map($callback, $this->items)))];
    }
}
