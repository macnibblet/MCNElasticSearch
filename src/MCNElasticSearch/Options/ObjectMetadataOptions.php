<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
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
     * @var string
     */
    protected $type;

    /**
     * Index name
     *
     * @var string
     */
    protected $index;

    /**
     * The name of the hydrator to load from they hydrator manager
     *
     * @var string
     */
    protected $hydrator;

    /**
     * FQCN of the object class anem
     *
     * @var string
     */
    protected $objectClassName;

    /**
     * @param string $hydrator
     */
    public function setHydrator($hydrator)
    {
        $this->hydrator = $hydrator;
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
        $this->id = $id;
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
     */
    public function setObjectClassName($objectClassName)
    {
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
        $this->type = $type;
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
        $this->index = $index;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }
}
