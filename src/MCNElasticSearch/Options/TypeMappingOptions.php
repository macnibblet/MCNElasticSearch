<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class TypeMappingOptions
 */
class TypeMappingOptions extends AbstractOptions
{
    /**
     * Type name
     *
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $index;

    /**
     * @var bool
     */
    protected $source = ['enabled' => true];

    /**
     * If the id is a path
     *
     * @var bool
     */
    protected $idIsPath = false;

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
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

    /**
     * @param boolean $source
     */
    public function setSource(array $source)
    {
        $this->source = $source;
    }

    /**
     * @return boolean
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param boolean $idIsPath
     */
    public function setIdIsPath($idIsPath)
    {
        $this->idIsPath = $idIsPath;
    }

    /**
     * @return boolean
     */
    public function getIdIsPath()
    {
        return $this->idIsPath;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
