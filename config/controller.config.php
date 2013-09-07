<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

use MCNElasticSearch\ControllerFactory\MappingControllerFactory;

return [
    'factories' => [
        'es.mapping' => MappingControllerFactory::class
    ]
];
