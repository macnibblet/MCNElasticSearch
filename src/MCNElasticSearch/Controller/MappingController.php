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

namespace MCNElasticSearch\Controller;

use Zend\Console\ColorInterface;
use Zend\Console\Prompt;
use MCNElasticSearch\Service\MappingServiceInterface;
use Zend\EventManager\Event;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Adapter\AdapterInterface as Console;

/**
 * Class MappingController
 *
 * @method \Zend\Console\Request getRequest
 */
class MappingController extends AbstractActionController
{
    /**
     * @var \MCNElasticSearch\Service\MappingServiceInterface
     */
    protected $service;

    /**
     * @var \Zend\Console\Adapter\AdapterInterface
     */
    protected $console;

    /**
     * @param \Zend\Console\Adapter\AdapterInterface            $console
     * @param \MCNElasticSearch\Service\MappingServiceInterface $service
     */
    public function __construct(Console $console, MappingServiceInterface $service)
    {
        $this->console = $console;
        $this->service = $service;
    }

    /**
     * Display a simple prompt
     *
     * Simple utility to display the prompt that is then mocked to simplify testing instead of inject a prompt bloating
     * the application.
     *
     * @codeCoverageIgnore
     *
     * @param string $message
     *
     * @return bool
     */
    protected function prompt($message = 'Are you sure you want to delete everything ?')
    {
        return (new Prompt\Confirm($message))->show();
    }

    /**
     * Report the progress of ongoing commands
     *
     * @param Event $event
     *
     * @return void
     */
    public function progress(Event $event)
    {
        /**
         * @var $response array
         * @var $metadata \MCNElasticSearch\Options\MetadataOptions
         */
        $response = $event->getParam('response');
        $metadata = $event->getParam('metadata');

        if (isset($response['acknowledged']) && $response['acknowledged']) {
            $this->console->write('[Success] ', ColorInterface::GREEN);
            $this->console->writeLine($metadata->getIndex() . '/' . $metadata->getType());
        } else {
            $this->console->write('[Error] ', ColorInterface::RED);
            $this->console->writeLine(sprintf('%s: %s', $metadata->getType(), $response['error']));
            $this->console->write(json_encode($event->getParam('mapping'), JSON_PRETTY_PRINT));
            $this->console->writeLine();
        }
    }

    /**
     * Create the schema
     */
    public function createAction()
    {
        $this->service->getEventManager()->attach('create', [$this, 'progress']);
        $this->service->create();
    }

    /**
     * Delete the entire mapping
     */
    public function deleteAction()
    {
        $skipPrompt = $this->getRequest()->getParam('y', false);

        if (! $skipPrompt && !$this->prompt()) {
            return;
        }

        $this->service->getEventManager()->attach('delete', [$this, 'progress']);
        $this->service->delete();
    }

    /**
     * Prune indexes that are missing from the configuration
     */
    public function pruneAction()
    {
        $skipPrompt = $this->getRequest()->getParam('y', false);

        if (! $skipPrompt && !$this->prompt('Are you sure you want to delete all unknown mappings?')) {
            return;
        }

        $this->service->getEventManager()->attach('prune', [$this, 'progress']);
        $this->service->prune();
    }
}
