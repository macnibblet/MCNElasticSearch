<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Container;

use MCNElasticSearch\QueryBuilder\Composite\CompositeInterface;

class SortContainer implements CompositeInterface
{
    protected $sort = [];

    /**
     * @param string $property
     * @param array  $options
     *
     * @throws Exception\InvalidArgumentException
     */
    public function add($property, array $options)
    {
        if (! isset($options['order'])) {
            throw new Exception\InvalidArgumentException();
        }

        $this->sort[] = [$property, $options];
    }

    public function isEmpty()
    {
        return empty($this->sort);
    }

    public function toArray()
    {
        foreach ($this->sort as list($property, $options)) {
            yield [$property => $options];
        }
    }
}
