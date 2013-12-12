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

use MCNElasticSearch\Options\MetadataOptions;
use MCNElasticSearch\Service\Document\Writer\WriterPluginManager;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Stdlib\Hydrator\HydratorPluginManager;

/**
 * Class DocumentService
 */
class DocumentService implements DocumentServiceInterface
{
    use EventManagerAwareTrait;

    /**
     * @var MetadataService
     */
    protected $metadata;

    /**
     * @var WriterPluginManager
     */
    protected $writerManager;

    /**
     * @var HydratorPluginManager
     */
    protected $hydratorManager;

    /**
     * @param MetadataServiceInterface $metadata
     * @param WriterPluginManager      $writerManager
     * @param HydratorPluginManager    $hydratorManager
     */
    public function __construct(
        MetadataServiceInterface $metadata,
        WriterPluginManager $writerManager,
        HydratorPluginManager $hydratorManager
    ) {
        $this->metadata        = $metadata;
        $this->writerManager   = $writerManager;
        $this->hydratorManager = $hydratorManager;
    }

    /**
     * Retrieve the object meta data
     *
     * @param $object
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\ObjectMetadataMissingException
     *
     * @return \MCNElasticSearch\Options\MetadataOptions
     */
    protected function getMetadata($object)
    {
        if (! is_object($object)) {
            throw Exception\InvalidArgumentException::invalidClass($object);
        }

        /** @var \Zend\Stdlib\Hydrator\AbstractHydrator $hydrator */
        return $this->metadata->getMetadata(get_class($object));
    }

    /**
     * Convert a object to a simple document
     *
     * @param mixed           $object
     * @param MetadataOptions $metadata
     *
     * @return Document\DocumentEntity
     */
    protected function createDocument($object, MetadataOptions $metadata)
    {
        $hydrator = $this->hydratorManager->get($metadata->getHydrator());

        $data = $hydrator->extract($object);
        $id   = isset($data[$metadata->getId()]) ? $data[$metadata->getId()] : null;

        return new Document\DocumentEntity($metadata->getIndex(), $metadata->getType(), $id, $data);
    }

    /**
     * Add a document
     *
     * @param mixed       $object
     * @param string|null $writer
     *
     * @throws Exception\InvalidArgumentException       If an invalid object is passed
     * @throws Exception\ObjectMetadataMissingException If the object metadata cannot be found
     *
     * @return void
     */
    public function add($object, $writer = null)
    {
        $metadata = $this->getMetadata($object);
        $document = $this->createDocument($object, $metadata);

        $writer = $writer ?: $metadata->getWriter();
        $writer = $this->writerManager->get($writer);
        $writer->insert($document);
    }

    /**
     * Update a document
     *
     * @param mixed       $object
     * @param string|null $writer
     *
     * @throws Exception\InvalidArgumentException       If an invalid object is passed
     * @throws Exception\ObjectMetadataMissingException If the object metadata cannot be found
     *
     * @return void
     */
    public function update($object, $writer = null)
    {
        $metadata = $this->getMetadata($object);
        $document = $this->createDocument($object, $metadata);

        $writer = $writer ?: $metadata->getWriter();
        $writer = $this->writerManager->get($writer);
        $writer->update($document);
    }

    /**
     * Deletes a document from it's index
     *
     * @param mixed       $object
     * @param string|null $writer
     *
     * @throws Exception\InvalidArgumentException       If an invalid object is passed
     * @throws Exception\ObjectMetadataMissingException If the object metadata cannot be found
     *
     * @return void
     */
    public function delete($object, $writer = null)
    {
        $metadata = $this->getMetadata($object);
        $document = $this->createDocument($object, $metadata);

        $writer = $writer ?: $metadata->getWriter();
        $writer = $this->writerManager->get($writer);
        $writer->insert($document);
    }
}
