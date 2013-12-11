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

use ArrayObject;
use Elastica\Document;
use Elastica\Response;
use Elastica\Client;
use MCNElasticSearch\Options\MetadataOptions;
use MCNElasticSearch\Service\Document\Writer\WriterInterface;
use MCNElasticSearch\Service\Document\Writer\WriterPluginManager;
use MCNElasticSearch\Service\DocumentService;
use MCNElasticSearch\Service\MetadataServiceInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\HydratorPluginManager;
use MCNElasticSearch\Service\Exception;

/**
 * Class DocumentServiceTest
 */
class DocumentServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $writerManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $hydratorPluginManager;

    /**
     * @var \MCNElasticSearch\Service\DocumentService
     */
    protected $service;

    protected function setUp()
    {
        $this->writerManager         = $this->getMock(WriterPluginManager::class);
        $this->metadataService       = $this->getMock(MetadataServiceInterface::class);
        $this->hydratorPluginManager = $this->getMock(HydratorPluginManager::class);

        $this->service = new DocumentService(
            $this->metadataService,
            $this->writerManager,
            $this->hydratorPluginManager
        );
    }

    public function invalidObjectData()
    {
        $object = new ArrayObject();

        return [
            ['add', 'foo',   Exception\InvalidArgumentException::class],
            ['add', $object, Exception\ObjectMetadataMissingException::class],
            ['add', $object, null],

            ['update', 'foo',   Exception\InvalidArgumentException::class],
            ['update', $object, Exception\ObjectMetadataMissingException::class],
            ['update', $object, null],

            ['delete', 'foo',   Exception\InvalidArgumentException::class],
            ['delete', $object, Exception\ObjectMetadataMissingException::class],
            ['delete', $object, null],
        ];
    }

    /**
     * Tests that add, update, delete all have the same internal API when transforming objects
     *
     * @dataProvider invalidObjectData
     *
     * @param string      $method       The service method to call
     * @param mixed       $object       The object passed to the service call
     * @param string|null $exception    Possible exception thrown by the transform method
     */
    public function testTransform($method, $object, $exception = null)
    {
        if ($exception !== null) {
            $this->setExpectedException($exception);

            $this->metadataService
                ->expects($this->any())
                ->method('getMetadata')
                ->will($this->throwException(new $exception()));

        } else {

            $metadata = new MetadataOptions();
            $metadata->setFromArray(
                [
                    'index' => 'hello',
                    'type' => 'world'
                ]
            );

            $this->metadataService
                ->expects($this->once())
                ->method('getMetadata')
                ->with(get_class($object))
                ->will($this->returnValue($metadata));

            $hydrator = $this->getMock(HydratorInterface::class);
            $hydrator
                ->expects($this->once())
                ->method('extract')
                ->will($this->returnValue(array()));

            $this->writerManager
                ->expects($this->once())
                ->method('get')
                ->will($this->returnValue($this->getMock(WriterInterface::class)));

            $this->hydratorPluginManager
                ->expects($this->once())
                ->method('get')
                ->will($this->returnValue($hydrator));
        }

        $this->service->{$method}($object);
    }
}
