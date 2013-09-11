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

namespace MCNElasticSearchTest\Service;

use Closure;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectManager;
use Elastica\Client;
use Elastica\Index;
use Elastica\Query;
use Elastica\Type;
use MCNElasticSearch\Options\ObjectMetadataOptions;
use MCNElasticSearch\Service\MetadataServiceInterface;
use MCNElasticSearch\Service\Search\PaginatorAdapter\AbstractAdapter;
use MCNElasticSearch\Service\Search\PaginatorAdapter\Doctrine;
use MCNElasticSearch\Service\Search\PaginatorAdapter\Raw;
use MCNElasticSearch\Service\SearchService;
use MCNElasticSearch\Service\SearchServiceInterface;
use PHPUnit_Framework_TestCase;
use MCNElasticSearch\Service\Exception;
use Zend\Paginator\Paginator;

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
        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager   = $this->getMock(ObjectManager::class);
        $this->metadataService = $this->getMock(MetadataServiceInterface::class);

        $this->service = new SearchService(
            $this->client,
            $this->metadataService,
            $this->objectManager
        );
    }

    public function testInvalidHydration()
    {
        $mode = 'do-not-exist-hydration-mode';

        $this->setExpectedException(
            Exception\InvalidArgumentException::class,
            sprintf('Unknown hydration mode %s', $mode)
        );

        $this->service->search('stdClass', new Query(), $mode);
    }

    public function dataHydrationModes()
    {
        $doctrineSetup = function () {
            $this->objectManager
                ->expects($this->once())
                ->method('getRepository')
                ->will($this->returnValue($this->getMock(Selectable::class)));
        };

        return [
            [SearchServiceInterface::HYDRATE_RAW, Raw::class],
            [SearchServiceInterface::HYDRATE_DOCTRINE_OBJECT, Doctrine::class, $doctrineSetup]
        ];
    }

    /**
     * @dataProvider dataHydrationModes
     *
     * @param string $mode
     * @param string $expectedAdapter
     * @param callable $setup
     */
    public function testValidHydrationMode($mode, $expectedAdapter, callable $setup = null)
    {
        if ($setup !== null) {
            $setup = Closure::bind($setup, $this);
            $setup();
        }

        $metadata = new ObjectMetadataOptions();

        $this->metadataService
            ->expects($this->once())
            ->method('getObjectMetadata')
            ->with('stdClass')
            ->will($this->returnValue($metadata));

        $type  = $this->getMockBuilder(Type::class)->disableOriginalConstructor()->getMock();
        $index = $this->getMockBuilder(Index::class)->disableOriginalConstructor()->getMock();

        $index->expects($this->once())
              ->method('getType')
              ->will($this->returnValue($type));

        $this->client
            ->expects($this->once())
            ->method('getIndex')
            ->will($this->returnValue($index));

        $query = new Query();

        $paginator = $this->service->search('stdClass', $query, $mode);
        $this->assertInstanceOf(Paginator::class, $paginator);

        $adapter = $paginator->getAdapter();
        $this->assertInstanceOf($expectedAdapter, $adapter);
        $this->assertInstanceOf(AbstractAdapter::class, $adapter);
    }
}
