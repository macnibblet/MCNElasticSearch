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
 * @author      Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright   2011-2014 Antoine Hedgecock
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace MCNElasticSearch\Service\Document\Writer\Adapter;

use Elasticsearch\Client;
use MCNElasticSearch\Service\Document\DocumentEntity;
use MCNElasticSearch\Service\Document\Writer\WriterInterface;

/**
 * Class Immediate
 *
 * A very basic writer that just pushes everything directly to elastic search
 */
class Immediate implements WriterInterface
{
    /**
     * @var \Elasticsearch\Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
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
        $doc = $document->toArray();

        // Move it
        $tmp = $doc['body'];
        unset($doc['body']);
        $doc['body']['doc'] = $tmp;

        $this->client->update($doc);
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
        $doc = $document->toArray();

        unset($doc['body']);

        $this->client->delete($doc);
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
        $this->client->index($document->toArray());
    }
}
