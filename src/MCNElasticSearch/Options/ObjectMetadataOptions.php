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

namespace MCNElasticSearch\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ObjectMetadataOptions
 */
class ObjectMetadataOptions extends AbstractOptions
{
    /**
     * Property to use as the document id
     *
     * @var string
     */
    protected $id = 'id';

    /**
     * Type name
     *
     * @var string|null
     */
    protected $type;

    /**
     * Index name
     *
     * @var string|null
     */
    protected $index;

    /**
     * The name of the hydrator to load from they hydrator manager
     *
     * @var string|null
     */
    protected $hydrator;

    /**
     * FQCN of the object class anem
     *
     * @var string|null
     */
    protected $objectClassName;

    /**
     * @param string $hydrator
     */
    public function setHydrator($hydrator)
    {
        $this->hydrator = (string) $hydrator;
    }

    /**
     * @return string
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = (string) $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $objectClassName
     *
     * @throws Exception\InvalidArgumentException If the given class name cannot be found
     */
    public function setObjectClassName($objectClassName)
    {
        if (! class_exists($objectClassName)) {
            throw new Exception\InvalidArgumentException(sprintf('Class %s could not be found', $objectClassName));
        }

        $this->objectClassName = $objectClassName;
    }

    /**
     * @return string
     */
    public function getObjectClassName()
    {
        return $this->objectClassName;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = (string)$type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $index
     */
    public function setIndex($index)
    {
        $this->index = (string)$index;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }
}
