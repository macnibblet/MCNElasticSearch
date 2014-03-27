<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder;

use MCNElasticSearch\QueryBuilder\Composite\Bool;
use MCNElasticSearch\QueryBuilder\Composite\CompositeInterface;

class HasParent implements CompositeInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var Composite\Bool
     */
    protected $query;

    /**
     * @var Composite\Bool
     */
    protected $filter;

    /**
     * @param $type
     */
    public function __construct($type)
    {
        $this->type   = (string) $type;
        $this->filter = new Bool();
        $this->query  = new Bool();
    }

    public function filter()
    {
        return $this->filter;
    }

    public function query()
    {
        return $this->query;
    }

    public function isEmpty()
    {
        return $this->filter->isEmpty() && $this->query->isEmpty();
    }

    public function toArray()
    {
        return ['has_parent', [
            'type'   => $this->type,
            'query'  => $this->query->toArray(),
            'filter' => $this->filter->toArray()
        ]];
    }
}
