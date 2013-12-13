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

use Doctrine\Common\Persistence\ObjectManager;
use Elasticsearch\Client;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Paginator\Paginator;

/**
 * Class SearchService
 */
class SearchService implements SearchServiceInterface
{
    use EventManagerAwareTrait;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @var MetadataService
     */
    protected $metadata;

    /**
     * @var \Elasticsearch\Client
     */
    protected $client;

    /**
     * @param \Elasticsearch\Client    $client
     * @param MetadataServiceInterface $metadata
     * @param ObjectManager            $objectManager
     */
    public function __construct(Client $client, MetadataServiceInterface $metadata, ObjectManager $objectManager)
    {
        $this->client        = $client;
        $this->metadata      = $metadata;
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $objectClassName
     * @param string $query
     * @param string $hydration
     * @param array  $options
     *
     * @throws Exception\InvalidArgumentException
     *
     * @return \Zend\Paginator\Paginator
     */
    public function search($objectClassName, $query, $hydration = self::HYDRATE_RAW, array $options = [])
    {
        $metadata = $this->metadata->getMetadata($objectClassName);

        switch ($hydration) {
            case static::HYDRATE_DOCTRINE:
                $adapter = new Search\PaginatorAdapter\Doctrine(
                    $this->objectManager->getRepository($objectClassName),
                    $metadata,
                    new Search\PaginatorAdapter\DoctrineOptions($options)
                );
                break;

            case static::HYDRATE_RAW:
                $adapter = new Search\PaginatorAdapter\Raw();
                break;

            default:
                throw new Exception\InvalidArgumentException(sprintf('Unknown hydration mode %s', $hydration));
        }

        $params = [
            'index' => $metadata->getIndex(),
            'type'  => $metadata->getType(),
            'body'  => $query
        ];

        $adapter->setQuery($params);
        $adapter->setClient($this->client);
        return new Paginator($adapter);
    }
}
