<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Filter;

class GeoDistance implements FilterInterface
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var array
     */
    private $coordinates;

    /**
     * @var string
     */
    private $distance;

    public function __construct($property, $coordinates, $distance)
    {
        $this->property = $property;
        $this->coordinates = $coordinates;
        $this->distance = $distance;
    }

    public function toArray()
    {
        return ['geo_distance', [
            'distance'      => $this->distance,
            $this->property => $this->coordinates
        ]];
    }
}
