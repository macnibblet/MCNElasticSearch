<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Query;

class Terms implements QueryInterface
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var array
     */
    private $values;
    /**
     * @var array
     */
    private $options;

    /**
     * @param string $property
     * @param array  $values
     * @param array  $options
     */
    public function __construct($property, array $values, array $options = [])
    {
        $this->values   = $values;
        $this->property = $property;
        $this->options  = $options;
    }

    public function toArray()
    {
        return ['terms', [
            $this->property => $this->values
        ] + $this->options];
    }
}
