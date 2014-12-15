<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Filter;

class MissingFilter implements FilterInterface
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var bool
     */
    private $nullValue;

    /**
     * @var bool
     */
    private $existence;

    /**
     * @param string $property
     * @param bool   $nullValue
     * @param bool   $existence
     */
    public function __construct($property, $nullValue = true, $existence = true)
    {
        $this->property  = $property;
        $this->nullValue = (bool) $nullValue;
        $this->existence = (bool) $existence;
    }

    public function toArray()
    {
        return [
            'missing',
            [
                'field'      => $this->property,
                'existence'  => $this->existence,
                'null_value' => $this->nullValue
            ]
        ];
    }
}
