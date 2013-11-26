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

namespace MCNElasticSearch\Service\Search\PaginatorAdapter;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;
use Elasticsearch\Query;
use Elasticsearch\ResultSet;
use MCNElasticSearch\Options\ObjectMetadataOptions;
use MCNElasticSearch\Service\Search\PaginatorAdapter\DoctrineOptions as Options;

/**
 * Class Doctrine
 */
class Doctrine extends AbstractAdapter
{
    /**
     * @var \Doctrine\Common\Collections\ObjectRepository
     */
    protected $repository;

    /**
     * @var \MCNElasticSearch\Options\ObjectMetadataOptions
     */
    protected $objectMetadata;

    /**
     * @var DoctrineOptions
     */
    protected $options;

    /**
     * @param \Doctrine\Common\Persistence\ObjectRepository   $repository
     * @param \MCNElasticSearch\Options\ObjectMetadataOptions $objectMetadata
     * @param DoctrineOptions                                 $options
     */
    public function __construct(ObjectRepository $repository, ObjectMetadataOptions $objectMetadata, Options $options)
    {
        $this->options        = $options;
        $this->repository     = $repository;
        $this->objectMetadata = $objectMetadata;
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

        $this->query->setFrom($offset);
        $this->query->setSize($itemCountPerPage);

        // Query elastic search
        $response = $this->searchable->search($this->query);

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
     * @param \Elasticsearch\ResultSet $results
     *
     * @return array
     */
    private function extractMetaInformation(ResultSet $results)
    {
        $dataSet = [];

        /** @var $result \Elasticsearch\Result */
        foreach ($results as $result) {
            $data = [];
            foreach ($result->getHit() as $key => $value) {
                if (substr($key, 0, 1) != '_') {
                    $data[$key] = $value;
                }
            }

            $dataSet[$result->getId()] = $data;
        }

        return $dataSet;
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

        return (new $className)->load($items, $this->repository, $this->objectMetadata);
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

            $method = 'get' . $this->objectMetadata->getId();
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
