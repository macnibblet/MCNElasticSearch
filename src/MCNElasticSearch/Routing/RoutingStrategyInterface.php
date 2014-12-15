<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\Routing;

interface RoutingStrategyInterface
{
    /**
     * Retrieve the routing for the given object
     *
     * @param mixed $object
     *
     * @throws Exception\InvalidObjectException
     *
     * @return string
     */
    public function getRouting($object);
}
