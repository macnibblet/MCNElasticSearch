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
use MCNElasticSearch\Options\ObjectMetadataOptions;
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
    protected $client;

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
        $this->client = $this->getMockBuilder(Client::class)
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->metadataService       = $this->getMock(MetadataServiceInterface::class);
        $this->hydratorPluginManager = $this->getMock(HydratorPluginManager::class);

        $this->service = new DocumentService(
            $this->client,
            $this->metadataService,
            $this->hydratorPluginManager
        );
    }

    public function invalidObjectData()
    {
        $object = new ArrayObject();

        return [
            ['add', 'addDocuments', Exception\InvalidArgumentException::class, 'foo'],
            ['add', 'addDocuments', Exception\ObjectMetadataMissingException::class, $object],
            ['add', 'addDocuments', $object, null],

            ['update', 'updateDocument', Exception\InvalidArgumentException::class, 'foo'],
            ['update', 'updateDocument', Exception\ObjectMetadataMissingException::class, $object],
            ['update', 'updateDocument', $object, null],

            ['delete', 'deleteDocuments', Exception\InvalidArgumentException::class, 'foo'],
            ['delete', 'deleteDocuments', Exception\ObjectMetadataMissingException::class, $object],
            ['delete', 'deleteDocuments', $object, null],
        ];
    }

    /**
     * Tests that add, update, delete all have the same internal API when transforming objects
     *
     * @dataProvider invalidObjectData
     *
     * @param string      $method       The service method to call
     * @param string      $clientMethod The internal method on the elastica client
     * @param mixed       $object       The object passed to the service call
     * @param string|null $exception    Possible exception thrown by the transform method
     */
    public function testTransform($method, $clientMethod, $object, $exception = null)
    {
        if ($exception !== null) {
            $this->setExpectedException($exception);

            $this->metadataService
                ->expects($this->any())
                ->method('getObjectMetadata')
                ->will($this->throwException(new Exception\ObjectMetadataMissingException()));

        } else {

            $metadata = new ObjectMetadataOptions();
            $metadata->setFromArray(
                [
                    'index' => 'hello',
                    'type' => 'world'
                ]
            );

            $this->metadataService
                ->expects($this->once())
                ->method('getObjectMetadata')
                ->with(get_class($object))
                ->will($this->returnValue($metadata));

            $this->hydratorPluginManager
                ->expects($this->once())
                ->method('get')
                ->will($this->returnValue($this->getMock(HydratorInterface::class)));

            $response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
            $response->expects($this->once())
                    ->method('isOk')
                    ->will($this->returnValue(true));

            $this->client
                ->expects($this->once())
                ->method($clientMethod)
                ->withAnyParameters()
                ->will($this->returnValue($response));
        }

        $this->service->{$method}($object);
    }

    public function eventData()
    {
        return [
            ['add', 'addDocuments', true],
            ['add', 'addDocuments', false],
            ['update', 'updateDocument', true],
            ['update', 'updateDocument', false],
            ['delete', 'deleteDocuments', true],
            ['delete', 'deleteDocuments', false],
        ];
    }

    /**
     * Checks that all the event parameters
     *
     * @dataProvider eventData
     *
     * @param string $method
     * @param string $clientMethod
     * @param bool   $responseIsOk
     */
    public function testEventParametersAndResponse($method, $clientMethod, $responseIsOk)
    {
        $object = new ArrayObject();
        $document = new Document();

        $response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
        $response->expects($this->once())
            ->method('isOk')
            ->will($this->returnValue($responseIsOk));

        if (! $responseIsOk) {
            $this->setExpectedException(Exception\RuntimeException::class);
        }

        $service = $this->getMockBuilder(DocumentService::class)
                        ->setConstructorArgs([$this->client, $this->metadataService, $this->hydratorPluginManager])
                        ->setMethods(['transform', 'getEventManager'])
                        ->getMock();

        $service->expects($this->once())
                ->method('transform')
                ->with($object)
                ->will($this->returnValue($document));

        $eventManager = $this->getMock(EventManagerInterface::class);
        $eventManager->expects($this->at(0))
            ->method('trigger')
            ->with($method . '.pre', $service, ['document' => $document, 'object' => $object]);

        $eventManager->expects($this->at(1))
            ->method('trigger')
            ->with(
                $method . '.post',
                $service,
                [
                    'document' => $document,
                    'object' => $object,
                    'response' => $response
                ]
            );

        $service->expects($this->exactly(2))
                ->method('getEventManager')
                ->will($this->returnValue($eventManager));

        $this->client
            ->expects($this->once())
            ->method($clientMethod)
            ->will($this->returnValue($response));

        $service->{$method}($object);
    }
}
