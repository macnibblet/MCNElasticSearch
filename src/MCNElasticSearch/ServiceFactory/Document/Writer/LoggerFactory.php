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
 * @author      Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright   2011-2013 Antoine Hedgecock
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace MCNElasticSearch\ServiceFactory\Document\Writer;

use MCNElasticSearch\Service\Document\Writer\Logger;
use MCNElasticSearch\Service\Document\Writer\LoggerOptions;
use MCNElasticSearch\ServiceFactory\Exception\DomainException;
use MCNElasticSearch\ServiceFactory\Exception\ConfigurationException;
use Psr\Log\LoggerInterface;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class LoggerFactory
 */
class LoggerFactory implements DelegatorFactoryInterface
{
    /**
     * A factory that creates delegates of a given service
     *
     * @param ServiceLocatorInterface $serviceLocator the service locator which requested the service
     * @param string                  $name           the normalized service name
     * @param string                  $requestedName  the requested service name
     * @param callable                $callback       the callback that is responsible for creating the service
     *
     * @throws DomainException        If the logger does not implement the psr-3 interface
     * @throws ConfigurationException If the configuration cannot be found
     *
     * @return \MCNElasticSearch\Service\Document\Writer\WriterInterface
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        $config = $serviceLocator->get('Config');
        if (! isset($config['MCNElasticSearch']['logging'])) {
            throw ConfigurationException::missingConfiguration('logging');
        }

        $loggerConfig = $config['MCNElasticSearch']['logging'];
        if (! $loggerConfig['enabled']) {
            return $callback();
        }

        $logger = $serviceLocator->get($loggerConfig['logger_service_name']);
        if (! $logger instanceof LoggerInterface) {
            throw new DomainException('The logger must implement the psr-3 logger interface.');
        }

        return new Logger($logger, new LoggerOptions($loggerConfig['options']), $callback());
    }
}
