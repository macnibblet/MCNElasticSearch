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

use Elasticsearch\Client;
use MCNElasticSearch\Service\Document\Writer\WriterPluginManager;
use MCNElasticSearch\Service\DocumentService;
use MCNElasticSearch\Service\MappingService;
use MCNElasticSearch\Service\MetadataService;
use MCNElasticSearch\Service\SearchService;
use MCNElasticSearch\ServiceFactory\ClientFactory;
use MCNElasticSearch\ServiceFactory\Document\Writer\WriterPluginManagerFactory;
use MCNElasticSearch\ServiceFactory\MappingServiceFactory;
use MCNElasticSearch\ServiceFactory\DocumentServiceFactory;
use MCNElasticSearch\ServiceFactory\MetadataServiceFactory;
use MCNElasticSearch\ServiceFactory\SearchServiceFactory;

/**
 * Service manager configuration for elastic search
 */
return [
    'factories' => [
        Client::class              => ClientFactory::class,
        SearchService::class       => SearchServiceFactory::class,
        MappingService::class      => MappingServiceFactory::class,
        DocumentService::class     => DocumentServiceFactory::class,
        MetadataService::class     => MetadataServiceFactory::class,
        WriterPluginManager::class => WriterPluginManagerFactory::class
    ]
];
