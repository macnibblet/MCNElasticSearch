<?php
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectManager;
use Elasticsearch\Client;
use MCNElasticSearch\Service\MetadataServiceInterface;
use MCNElasticSearch\Service\Search\Criteria\ExpressionBuilder;
use MCNElasticSearch\Service\SearchService;

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

/**
 * Class SearchServiceTest
 */
class SearchServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $client;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataService;

    /**
     * @var \MCNElasticSearch\Service\SearchService
     */
    protected $service;

    protected function setUp()
    {
        $this->client          = $this->getMock(Client::class);
        $this->objectManager   = $this->getMock(ObjectManager::class);
        $this->metadataService = $this->getMock(MetadataServiceInterface::class);

        $this->service = new SearchService(
            $this->client,
            $this->metadataService,
            $this->objectManager
        );
    }

    public function testCriteria()
    {
        $expr = new ExpressionBuilder();

        $query = Criteria::create();
        $query->andWhere(
            $expr->query(
                $expr->filtered(
                    $expr->andX(
                        $expr->eq('term', 'foo'),
                        $expr->eq('term', 'foo')
                    )
                )
            )
        );

        $query->andWhere(
            $expr->query(
                $expr->filtered(
                    $expr->andX(
                        $expr->eq('term', 'foo'),
                        $expr->eq('term', 'foo')
                    )
                )
            )
        );

        $this->service->match($query);
    }
}
