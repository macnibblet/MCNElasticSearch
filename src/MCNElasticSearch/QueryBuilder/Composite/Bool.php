<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Composite;

use MCNElasticSearch\QueryBuilder\ExpressionInterface;

class Bool implements CompositeInterface
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

    public function isEmpty()
    {
        return empty($this->must) && empty($this->mustNot) && empty($this->should);
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
