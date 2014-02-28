<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Container;

use MCNElasticSearch\QueryBuilder\Filter\FilterInterface;

class FilterContainer
{
    protected $filters = [];

    public function add(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    public function toArray()
    {
    }
}
