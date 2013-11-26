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
 * @author      Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright   2011-2013 Antoine Hedgecock
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace MCNElasticSearch\Service\Document\Writer;

use MCNElasticSearch\Service\Document\DocumentEntity;
use Psr\Log\LoggerInterface;

/**
 * Class logger
 *
 * Encapsulates the the real logger and adds logging functionality to it.
 */
class Logger implements WriterInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var WriterInterface
     */
    protected $writer;

    /**
     * @var LoggerOptions
     */
    protected $options;

    /**
     * @param LoggerInterface $logger
     * @param LoggerOptions   $options
     * @param WriterInterface $writer
     */
    public function __construct(LoggerInterface $logger, LoggerOptions $options, WriterInterface $writer)
    {
        $this->writer  = $writer;
        $this->logger  = $logger;
        $this->options = $options;
    }

    /**
     * Update a document
     *
     * @param \MCNElasticSearch\Service\Document\DocumentEntity $document
     *
     * @return void
     */
    public function update(DocumentEntity $document)
    {
        $this->writer->update($document);

        $message = sprintf(
            'updated the document id: %s, type: %s, index: %s',
            $document['id'],
            $document['type'],
            $document['index']
        );

        $this->logger->log($this->options->getLogLevel(), $message, $document['body']);
    }

    /**
     * Delete a document
     *
     * @param \MCNElasticSearch\Service\Document\DocumentEntity $document
     *
     * @return void
     */
    public function delete(DocumentEntity $document)
    {
        $this->writer->delete($document);

        $message = sprintf(
            'deleted the document id: %s, type: %s, index: %s',
            $document['id'],
            $document['type'],
            $document['index']
        );

        $this->logger->log($this->options->getLogLevel(), $message, $document['body']);
    }

    /**
     * Insert a document
     *
     * @param \MCNElasticSearch\Service\Document\DocumentEntity $document
     *
     * @return void
     */
    public function insert(DocumentEntity $document)
    {
        $this->writer->insert($document);

        $message = sprintf(
            'created new document type: %s, index: %s',
            $document['type'],
            $document['index']
        );

        $this->logger->log($this->options->getLogLevel(), $message, $document['body']);
    }
}
