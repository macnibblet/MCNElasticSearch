<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Filter;

class RangeFilter implements FilterInterface
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var array
     */
    private $value;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string $property
     * @param array  $value
     * @param array  $options
     */
    public function __construct($property, $value, array $options = [])
    {
        $this->value   = $value;
        $this->property = $property;
        $this->options  = $options;
    }

    public function toArray()
    {
        return ['range', [
            $this->property => $this->value
        ] + $this->options ];
    }
}
