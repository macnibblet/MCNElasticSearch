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

namespace MCNElasticSearch\Service\Search\Paginator\Adapter;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;
use MCNElasticSearch\Options\MetadataOptions;
use MCNElasticSearch\Service\Search\PaginatorAdapter\DoctrineOptions as Options;

/**
 * Class Doctrine
 */
class Doctrine extends AbstractAdapter
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository;

    /**
     * @var \MCNElasticSearch\Options\MetadataOptions
     */
    protected $metadata;

    /**
     * @var DoctrineOptions
     */
    protected $options;

    /**
     * @param \Doctrine\Common\Persistence\ObjectRepository   $repository
     * @param \MCNElasticSearch\Options\MetadataOptions       $metadata
     * @param DoctrineOptions                                 $options
     */
    public function __construct(ObjectRepository $repository, MetadataOptions $metadata, Options $options)
    {
        $this->options    = $options;
        $this->metadata   = $metadata;
        $this->repository = $repository;
    }

    /**
     * Returns an collection of items for a page.
     *
     * @param int $offset
     * @param int $itemCountPerPage
     *
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        if ($this->count() == 0) {
            return [];
        }

        $this->query['from'] = $offset;
        $this->query['size'] = $itemCountPerPage;

        // Query elastic search
        $response = $this->client->search($this->query);

        $meta  = $this->extractMetaInformation($response);
        $items = $this->load(array_keys($meta));

        return $this->merge($meta, $items);
    }

    /**
     * Extract meta information from each result
     *
     * When doing queries against elastic search one can aggregate meta information and this is where we extract it
     * from each result.
     *
     * @param array $response
     *
     * @return array
     */
    private function extractMetaInformation(array $response)
    {
        if (isset($this->query['body']['sort'])) {
            $sortingKeys = array_keys($this->query['body']['sort']);
        }

        $result = [];
        foreach ($response['hits']['hits'] as $hit) {
            $result[$hit['_id']] = [];

            if (isset($sortingKeys)) {
                $result[$hit['_id']]['sort'] = [];
                foreach ($hit['sort'] as $index => $value) {
                    $result[$hit['_id']]['sort'][$sortingKeys[$index]] = $value;
                }
            }

            if (isset($hit['fields'])) {
                $result[$hit['_id']]['fields'] = $hit['fields'];
            }
        }

        return $result;
    }

    /**
     * Get all the objects from doctrine
     *
     * @param array $items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    protected function load(array $items)
    {
        $className = $this->options->getStrategy();

        return (new $className)->load($items, $this->repository, $this->metadata);
    }

    /**
     * Merge meta data and sort
     *
     * Merges the meta data if any exists into the result set, and due to the nature of SQL IN we also need to sort
     * the documents according to the results of the elastic query search
     *
     * @param array      $meta
     * @param Collection $items
     *
     * @return array
     */
    protected function merge(array $meta, Collection $items)
    {
        $result  = [];
        $sorting = array_keys($meta);

        foreach ($items as $item) {
            $method = 'get' . $this->metadata->getId();
            $id = $item->{$method}();

            if (! empty($meta[$id])) {
                $item = [$item] + $meta[$id];
            }

            $result[array_search($id, $sorting)] = $item;
        }

        ksort($result);

        return $result;
    }
}
