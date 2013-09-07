<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service;
use Elastica\Client;
use MCNElasticSearch\Options\ObjectMetadataOptions;
use MCNElasticSearch\Options\TypeMappingOptions;

/**
 * Class ConfigurationService
 */
class ConfigurationService implements ConfigurationServiceInterface
{
    /**
     * @var \Elastica\Client
     */
    protected $client;

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
    public function __construct(array $configuration)
    {
        $this->setConfiguration($configuration);
    }

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration)
    {
        if (isset($configuration['client'])) {
            $this->getClient()->setConfig($configuration['client']);
        }

        if (isset($configuration['object_metadata'])) {

            // reset
            $this->objectMetadata = [];
            foreach ($configuration['object_metadata'] as $className => $config) {
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
     * @return \Elastica\Client
     */
    public function getClient()
    {
        if ($this->client === null) {
            $this->client = new Client();
        }

        return $this->client;
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
