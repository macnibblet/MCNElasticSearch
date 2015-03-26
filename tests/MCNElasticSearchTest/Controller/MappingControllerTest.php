<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearchTest\Controller;

use MCNElasticSearch\Controller\MappingController;
use MCNElasticSearch\Service\MappingServiceInterface;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Console\Request;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

/**
 * Class MappingControllerTest
 */
class MappingControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $evm;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $console;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mappingService;

    /**
     * @var \MCNElasticSearch\Controller\MappingController
     */
    protected $controller;

    /**
     * @var \Zend\Console\Request
     */
    protected $request;

    /**
     * @var \Zend\Mvc\Router\RouteMatch
     */
    protected $routeMatch;

    protected function setUp()
    {
        // controller dependencies
        $this->evm            = $this->getMock(EventManagerInterface::class);
        $this->console        = $this->getMock(AdapterInterface::class);
        $this->mappingService = $this->getMock(MappingServiceInterface::class);
        $this->mappingService
            ->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($this->evm));

        $this->controller = $this->getMockBuilder(MappingController::class)
                                 ->setMethods(['prompt'])
                                 ->setConstructorArgs([$this->console, $this->mappingService])
                                 ->getMock();

        // controller configuration
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(['controller' => MappingController::class]);

        $event = new MvcEvent();
        $event->setRequest($this->request);
        $event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($event);
    }

    public function testCreate()
    {
        $this->evm
            ->expects($this->once())
            ->method('attach')
            ->with('create', [$this->controller, 'progress']);

        $this->mappingService
            ->expects($this->once())
            ->method('create');

        $this->controller->createAction();
    }

    public function testDelete_skipPrompt()
    {
        $this->routeMatch->setParam('action', 'delete');
        $this->request->getParams()->set('y', true);

        $this->evm
            ->expects($this->once())
            ->method('attach')
            ->with('delete', [$this->controller, 'progress']);

        $this->mappingService
            ->expects($this->once())
            ->method('delete');

        $this->controller->dispatch($this->request);
    }

    public function testDelete_failPrompt()
    {
        $this->routeMatch->setParam('action', 'delete');
        $this->request->getParams()->set('y', false);

        $this->mappingService
            ->expects($this->never())
            ->method('delete');

        $this->controller
            ->expects($this->once())
            ->method('prompt')
            ->will($this->returnValue(false));

        $this->controller->dispatch($this->request);
    }

    public function testDelete_ConfirmPrompt()
    {
        $this->routeMatch->setParam('action', 'delete');
        $this->request->getParams()->set('y', false);

        $this->controller
            ->expects($this->once())
            ->method('prompt')
            ->will($this->returnValue(true));

        $this->evm
            ->expects($this->once())
            ->method('attach')
            ->with('delete', [$this->controller, 'progress']);

        $this->mappingService
            ->expects($this->once())
            ->method('delete');

        $this->controller->dispatch($this->request);
    }

    public function testPrune_skipPrompt()
    {
        $this->routeMatch->setParam('action', 'prune');
        $this->request->getParams()->set('y', true);

        $this->evm
            ->expects($this->once())
            ->method('attach')
            ->with('prune', [$this->controller, 'progress']);

        $this->mappingService
            ->expects($this->once())
            ->method('prune');

        $this->controller->dispatch($this->request);
    }

    public function testPrune_failPrompt()
    {
        $this->routeMatch->setParam('action', 'prune');
        $this->request->getParams()->set('y', false);

        $this->mappingService
            ->expects($this->never())
            ->method('prune');

        $this->controller
            ->expects($this->once())
            ->method('prompt')
            ->will($this->returnValue(false));

        $this->controller->dispatch($this->request);
    }

    public function testPrune_ConfirmPrompt()
    {
        $this->routeMatch->setParam('action', 'prune');
        $this->request->getParams()->set('y', false);

        $this->controller
            ->expects($this->once())
            ->method('prompt')
            ->will($this->returnValue(true));

        $this->evm
            ->expects($this->once())
            ->method('attach')
            ->with('prune', [$this->controller, 'progress']);

        $this->mappingService
            ->expects($this->once())
            ->method('prune');

        $this->controller->dispatch($this->request);
    }
}
