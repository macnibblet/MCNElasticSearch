<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Container;

use MCNElasticSearch\QueryBuilder\Composite\CompositeInterface;

class ScriptFieldContainer implements CompositeInterface
{
    protected $scripts = [];

    public function add($name, $parameters)
    {
        $this->scripts[$name] = $parameters;
    }

    public function isEmpty()
    {
        return empty($this->scripts);
    }

    public function toArray()
    {
        foreach ($this->scripts as $property => $parameters) {
            yield $property => $parameters;
        }
    }
}
