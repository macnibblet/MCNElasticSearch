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

namespace MCNElasticSearch\Service;

use Elastica\Client;
use Elastica\Document;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Stdlib\Hydrator\HydratorPluginManager;

/**
 * Class DocumentService
 */
class DocumentService implements DocumentServiceInterface
{
    use EventManagerAwareTrait;

    /**
     * @var \Elastica\Client
     */
    protected $client;

    /**
     * @var MetadataService
     */
    protected $metadata;

    /**
     * @var \Zend\Stdlib\Hydrator\HydratorPluginManager
     */
    protected $hydratorManager;

    /**
     * @param \Elastica\Client $client
     * @param MetadataServiceInterface $metadata
     * @param \Zend\Stdlib\Hydrator\HydratorPluginManager $hydratorManager
     */
    public function __construct(
        Client $client,
        MetadataServiceInterface $metadata,
        HydratorPluginManager $hydratorManager
    ) {
        $this->client          = $client;
        $this->metadata        = $metadata;
        $this->hydratorManager = $hydratorManager;
    }

    /**
     * Converts an object into a elastica document
     *
     * @param mixed $object
     * @throws Exception\InvalidArgumentException If an invalid object is passed
     * @return \Elastica\Document
     */
    protected function transform($object)
    {
        if (! is_object($object)) {
            throw Exception\InvalidArgumentException::invalidClass($object);
        }

        /** @var \Zend\Stdlib\Hydrator\AbstractHydrator $hydrator */
        $metadata = $this->metadata->getObjectMetadata(get_class($object));
        $hydrator = $this->hydratorManager->get($metadata->getHydrator());

        // extract data
        $data = $hydrator->extract($object);

        // transform it to a document
        return new Document($data[$metadata->getId()], $data, $metadata->getType(), $metadata->getIndex());
    }

    /**
     * Add a document
     *
     * @param mixed $object
     *
     * @triggers add.pre
     * @triggers add.post
     *
     * @throws Exception\InvalidArgumentException       If an invalid object is passed
     * @throws Exception\ObjectMetadataMissingException If the object metadata cannot be found
     * @throws Exception\RuntimeException               In case something goes wrong during persisting the document
     *
     * @return void
     */
    public function add($object)
    {
        $document = $this->transform($object);

        $this->getEventManager()
             ->trigger(__FUNCTION__ . '.pre', $this, compact('document', 'object'));

        $response = $this->client->addDocuments([$document]);

        $this->getEventManager()
             ->trigger(__FUNCTION__ . '.post', $this, compact('document', 'object', 'response'));

        if (! $response->isOk()) {
            throw new Exception\RuntimeException($response->getError());
        }
    }

    /**
     * Update a document
     *
     * @param mixed $object
     *
     * @triggers update.pre
     * @triggers update.post
     *
     * @throws Exception\InvalidArgumentException       If an invalid object is passed
     * @throws Exception\ObjectMetadataMissingException If the object metadata cannot be found
     * @throws Exception\RuntimeException               In case something goes wrong during an update
     *
     * @return void
     */
    public function update($object)
    {
        $document = $this->transform($object);

        $this->getEventManager()
             ->trigger(__FUNCTION__ . '.pre', $this, compact('document', 'object'));

        $response = $this->client->updateDocument(
            $document->getId(),
            $document->getData(),
            $document->getIndex(),
            $document->getType()
        );

        $this->getEventManager()
             ->trigger(__FUNCTION__ . '.post', $this, compact('document', 'object', 'response'));

        if (! $response->isOk()) {
            throw new Exception\RuntimeException($response->getError());
        }
    }

    /**
     * Deletes a document from it's index
     *
     * @param mixed $object
     *
     * @triggers delete.pre
     * @triggers delete.post
     *
     * @throws Exception\InvalidArgumentException       If an invalid object is passed
     * @throws Exception\ObjectMetadataMissingException If the object metadata cannot be found
     * @throws Exception\RuntimeException               In case something goes wrong during an update
     *
     * @return void
     */
    public function delete($object)
    {
        $document = $this->transform($object);

        $this->getEventManager()
             ->trigger(__FUNCTION__ . '.pre', $this, compact('document', 'object'));

        $response = $this->client->deleteDocuments([$document]);

        $this->getEventManager()
             ->trigger(__FUNCTION__ . '.post', $this, compact('document', 'object', 'response'));

        if (! $response->isOk()) {
            throw new Exception\RuntimeException($response->getError());
        }
    }
}
