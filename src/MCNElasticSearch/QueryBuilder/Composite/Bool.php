<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Composite;

use MCNElasticSearch\QueryBuilder\ExpressionInterface;
use MCNElasticSearch\QueryBuilder\Filter\FilterInterface;

class Bool implements ExpressionInterface
{
    protected $must = [];

    protected $mustNot = [];

    protected $should = [];

    public function must(ExpressionInterface $expression)
    {
        $this->must[] = $expression;
    }

    public function mustNot(ExpressionInterface $expression)
    {
        $this->mustNot[] = $expression;
    }

    public function should(ExpressionInterface $expression)
    {
        $this->should[] = $expression;
    }

    public function toArray()
    {
        $callback = function (ExpressionInterface $filter) {
            list ($method, $body) = $filter->toArray();

            return [$method => $body];
        };

        return ['bool' => [
            'must'     => array_map($callback, $this->must),
            'must_not' => array_map($callback, $this->mustNot),
            'should'   => array_map($callback, $this->should)
        ]];
    }
}
