<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Facet;

class TermsFacet implements FacetInterface
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var array
     */
    private $options;
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     * @param string $property
     * @param array  $options
     */
    public function __construct($name, $property, array $options = [])
    {
        $this->name     = $name;
        $this->property = $property;
        $this->options  = $options;
    }

    public function getName()
    {
        return $this->name;
    }

    public function toArray()
    {
        return ['terms', [
            'field' => $this->property
        ] + $this->options];
    }
}
