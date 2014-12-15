<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Query;

class Term implements QueryInterface
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $value;

    /**
     * @var float
     */
    private $boost;

    /**
     * @param string $property
     * @param string $value
     * @param float  $boost
     */
    public function __construct($property, $value, $boost = 1.0)
    {
        $this->value    = $value;
        $this->property = $property;
        $this->boost    = $boost;
    }

    public function toArray()
    {
        return ['term', [
            $this->property => [
                'value' => $this->value,
                'boost' => (string) $this->boost
            ]
        ]];
    }
}
