<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */
return [
    'es-delete-mapping' => [
        'options' => [
            'route'    => 'es mapping delete [-y]',
            'defaults' => [

                'controller' => 'es.mapping',
                'action'     => 'delete-mapping',
            ]
        ]
    ],

    'es-build-mapping' => [
        'options' => [
            'route'    => 'es mapping build',
            'defaults' => [

                'controller' => 'es.mapping',
                'action'     => 'build-mapping',
            ]
        ]
    ],
];
