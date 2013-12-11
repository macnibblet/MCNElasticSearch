<?php
/**
 * Copyright (c) 2011-2013 Antoine Hedgecock.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author      Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright   2011-2013 Antoine Hedgecock
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

use MCNElasticSearch\Service\Document\Writer\Adapter\DevNull;
use MCNElasticSearch\Service\Document\Writer\Adapter\Immediate;
use MCNElasticSearch\Service\DocumentService;
use MCNElasticSearch\Service\MappingService;
use MCNElasticSearch\Service\SearchService;
use MCNElasticSearch\ServiceFactory\Document\Writer\Adapter\ImmediateFactory;
use MCNElasticSearch\Service\Document\Writer\Logger;
use MCNElasticSearch\ServiceFactory\Document\Writer\LoggerFactory;
use Psr\Log\LogLevel;

return [
    'MCNElasticSearch' => [

        /**
         * Client configuration
         */
        'client' => [],

        /**
         * Logging configuration
         */
        'logging' => [

            /**
             * If the logger should be enabled
             */
            'enabled' => false,

            /**
             * The key used to get the logger utility from the service locator
             */
            'logger_service_name'  => null,

            /**
             * Options that are passed to the MCNElasticSearch\Document\Writer\LoggerOptions
             */
            'options' => [
                'logLevel' => LogLevel::NOTICE
            ]
        ],

        /**
         * Metadata configuration
         */
        'metadata' => [

        ],

        /**
         * Plugin manager for the different writers available
         */
        'writer_manager' => [
            'invokables' => [
                DevNull::class => DevNull::class
            ],

            'factories' => [
                Logger::class    => LoggerFactory::class,
                Immediate::class => ImmediateFactory::class
            ]
        ],

        DocumentService::class => [
            'listeners' => []
        ],

        SearchService::class => [
            'listeners' => []
        ],

        MappingService::class => [
            'listeners' => []
        ]
    ],

    'console'         => ['router' => ['routes' => include __DIR__ . '/console-routes.config.php']],
    'service_manager' => include __DIR__ . '/service.config.php',
    'controllers'     => include __DIR__ . '/controller.config.php'
];
