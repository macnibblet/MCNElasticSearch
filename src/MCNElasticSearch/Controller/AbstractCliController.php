<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Controller;

use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;

/**
 * Class AbstractCliController
 *
 * @method \Zend\Console\Request getRequest
 * @method \Zend\Console\Response getResponse
 */
abstract class AbstractCliController extends AbstractActionController
{
    /**
     * @var \Zend\Console\Adapter\AdapterInterface
     */
    protected $console;

    /**
     * Lets just validate we are in a console environment
     *
     * @param MvcEvent $e
     *
     * @return mixed
     */
    public function onDispatch(MvcEvent $e)
    {
        if (! $this->getRequest() instanceof ConsoleRequest) {
            throw new \LogicException('Request may only be done via the CLI');
        }

        $this->console = $this->getServiceLocator()->get('console');
        return parent::onDispatch($e);
    }
}
