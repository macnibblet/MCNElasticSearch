<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Filter;

use MCNElasticSearch\QueryBuilder\ExpressionInterface;

class NestedFilter implements FilterInterface
{
    public function __construct($path, ExpressionInterface $filter)
    {
        $this->path   = $path;
        $this->filter = $filter;
    }

    public function toArray()
    {
        return ['nested', [
            'path'   => $this->path,
            'filter' => $this->filter->toArray()
        ]];
    }
}
