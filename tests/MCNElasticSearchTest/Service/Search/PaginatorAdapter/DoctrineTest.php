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

namespace MCNElasticSearchTest\Service\Search\PaginatorAdapter;

use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;
use Elastica\SearchableInterface;
use MCNElasticSearch\Options\ObjectMetadataOptions;
use MCNElasticSearch\Service\Search\PaginatorAdapter\Doctrine;
use MCNElasticSearch\Service\Search\PaginatorAdapter\DoctrineOptions;

/**
 * Class DoctrineTest
 */
class DoctrineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchable;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadata;

    /**
     * @var \MCNElasticSearch\Service\Search\PaginatorAdapter\Doctrine
     */
    protected $adapter;

    /**
     * @var DoctrineOptions
     */
    protected $options;

    protected function setUp()
    {
        $this->searchable = $this->getMock(SearchableInterface::class);
        $this->repository = $this->getMock(ObjectRepository::class);
        $this->metadata   = $this->getMock(ObjectMetadataOptions::class);
        $this->options    = new DoctrineOptions();

        $this->adapter = new Doctrine(
            $this->repository,
            $this->metadata,
            $this->options
        );

        $this->adapter->setSearchable($this->searchable);
    }

    public function testGetItems_ShortCircuitOnZeroCount()
    {
        $this->searchable
            ->expects($this->once())
            ->method('count')
            ->will($this->returnValue(0));

        $this->searchable
            ->expects($this->never())
            ->method('search');

        $this->adapter->getItems(0, 10);
    }
}
