<?php
/**
 * Copyright (c) 2011-2014 Antoine Hedgecock.
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
 * @copyright   2011-2014 Antoine Hedgecock
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace MCNElasticSearch\Service;

use Elasticsearch\Client;
use Exception;
use MCNElasticSearch\Options\MetadataOptions;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class MappingService
 */
class MappingService implements MappingServiceInterface
{
    use EventManagerAwareTrait;

    /**
     * @var \Elasticsearch\Client
     */
    protected $client;

    /**
     * @var MetadataService
     */
    protected $metadata;

    /**
     * @param \Elasticsearch\Client $client
     * @param MetadataService       $metadata
     */
    public function __construct(Client $client, MetadataService $metadata)
    {
        $this->client   = $client;
        $this->metadata = $metadata;
    }

    /**
     * Creates the mapping of all or a list of given types
     *
     * Be aware that to properly handle the response you must listen to the create event
     *
     * @param array $types List of type names to build
     *
     * @return void
     */
    public function create(array $types = [])
    {
        $list = $this->metadata->getAllMetadata();

        if (! empty($types)) {
            array_filter($list, function (MetadataOptions $t) use ($types) {
                return in_array($t->getType(), $types);
            });
        }

        $extract = function (MetadataOptions $metadata) {
            return $metadata->getIndex();
        };

        $indexes = array_map($extract, $list);
        $indexes = array_unique($indexes);

        array_walk($indexes, function ($index) {
            if (! $this->client->indices()->exists(['index' => $index])) {
                $this->client->indices()->create(['index' => $index]);
            }
        });

        /** @var $metadata \MCNElasticSearch\Options\MetadataOptions */
        foreach ($list as $metadata) {
            try {
                $mapping = [
                    'index' => $metadata->getIndex(),
                    'type'  => $metadata->getType(),
                    'body'  => [
                        $metadata->getType() => $metadata->getMapping()
                    ]
                ];

                $response = $this->client->indices()->putMapping($mapping);

            } catch (Exception $exception) {
                $response = ['ok' => false, 'error' => $exception->getMessage()];
            } finally {
                $this->getEventManager()
                     ->trigger('create', $this, compact('mapping', 'response', 'metadata'));
            }
        }
    }

    /**
     * Delete the entire mapping or a specific part
     *
     * Be aware that to properly handle the response you must listen to the delete event
     *
     * @param array $types
     *
     * @return void
     */
    public function delete(array $types = [])
    {
        $list = $this->metadata->getAllMetadata();

        if (! empty($types)) {
            array_filter($list, function (MetadataOptions $t) use ($types) {
                return in_array($t->getType(), $types);
            });
        }

        /** @var $metadata \MCNElasticSearch\Options\MetadataOptions */
        foreach ($list as $metadata) {
            try {
                $params = [
                    'index' => $metadata->getIndex(),
                    'type'  => $metadata->getType()
                ];

                $response = $this->client->indices()->deleteMapping($params);

            } catch (Exception $exception) {
                $response = [
                    'ok'    => false,
                    'error' => $exception->getMessage()
                ];

            } finally {

                $this->getEventManager()
                    ->trigger('delete', $this, compact('response', 'metadata'));
            }
        }
    }

    /**
     * Prune removed mappings from Elastic Search
     *
     * Be aware that to properly handle the response you must listen to the delete event
     *
     * @param array $types
     *
     * @return void
     */
    public function prune()
    {
        $list = $this->metadata->getAllMetadata();

        $pruneMappings = $this->client->indices()->getMapping();

        # Remove configured mappings from prune list
        foreach ($list as $metadata) {
            $index = $metadata->getIndex();
            $type = $metadata->getType();

            unset($pruneMappings[$index]['mappings'][$type]);
        }

        # Delete mappings that are not in the configuration
        foreach ($pruneMappings as $index => $indexMapping) {
            foreach ($indexMapping['mappings'] as $type => $typeMapping) {
                try {
                    $params = [
                        'index' => $index,
                        'type' => $type
                    ];

                    $response = $this->client->indices()->deleteMapping($params);
                } catch (Exception $exception) {
                    $response = [
                        'ok' => false,
                        'error' => $exception->getMessage()
                    ];
                } finally {
                    $metadata = new MetadataOptions;
                    $metadata->setIndex($index);
                    $metadata->setType($type);

                    $this->getEventManager()
                        ->trigger('prune', $this, compact('response', 'metadata'));
                }
            }
        }
    }
}
