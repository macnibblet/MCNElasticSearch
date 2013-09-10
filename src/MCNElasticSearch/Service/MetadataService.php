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

use Elastica\Client;
use MCNElasticSearch\Options\ObjectMetadataOptions;
use MCNElasticSearch\Options\TypeMappingOptions;

/**
 * Class MetadataService
 */
class MetadataService implements MetadataServiceInterface
{
    /**
     * @var \MCNElasticSearch\Options\TypeMappingOptions[]
     */
    protected $typeMapping = [];

    /**
     * @var \MCNElasticSearch\Options\ObjectMetadataOptions[]
     */
    protected $objectMetadata = [];

    /**
     * @param array $configuration
     */
    public function __construct(array $configuration = [])
    {
        $this->setConfiguration($configuration);
    }

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration)
    {
        if (isset($configuration['objects'])) {

            // reset
            $this->objectMetadata = [];
            foreach ($configuration['objects'] as $className => $config) {
                $this->objectMetadata[$className] = new ObjectMetadataOptions($config);
                $this->objectMetadata[$className]->setObjectClassName($className);
            }
        }

        if (isset($configuration['types'])) {

            // reset
            $this->typeMapping = [];
            foreach ($configuration['types'] as $typeName => $config) {
                $this->typeMapping[$typeName] = new TypeMappingOptions($config);
                $this->typeMapping[$typeName]->setName($typeName);
            }
        }
    }

    /**
     * @param string $className
     *
     * @throws Exception\ObjectMetadataMissingException
     *
     * @return \MCNElasticSearch\Options\ObjectMetadataOptions
     */
    public function getObjectMetadata($className)
    {
        if (isset($this->objectMetadata[$className])) {
            return $this->objectMetadata[$className];
        }

        throw new Exception\ObjectMetadataMissingException($className);
    }

    /**
     * @param string $type
     *
     * @throws Exception\TypeMappingMissingException
     *
     * @return \MCNElasticSearch\Options\TypeMappingOptions
     */
    public function getTypeMapping($type)
    {
        if (isset($this->typeMapping[$type])) {
            return $this->typeMapping[$type];
        }

        throw new Exception\TypeMappingMissingException($type);
    }

    /**
     * @return \MCNElasticSearch\Options\TypeMappingOptions[]
     */
    public function getAllTypeMappings()
    {
        return $this->typeMapping;
    }
}
