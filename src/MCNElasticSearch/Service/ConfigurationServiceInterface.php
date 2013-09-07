<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service;

/**
 * Interface ConfigurationServiceInterface
 */
interface ConfigurationServiceInterface
{
    /**
     * @return \Elastica\Client
     */
    public function getClient();

    /**
     * @param string $className
     *
     * @throws Exception\ObjectMetadataMissingException
     *
     * @return \MCNElasticSearch\Options\ObjectMetadataOptions
     */
    public function getObjectMetadata($className);

    /**
     * @param string $type
     *
     * @throws Exception\TypeMappingMissingException
     *
     * @return \MCNElasticSearch\Options\TypeMappingOptions
     */
    public function getTypeMapping($type);

    /**
     * @return \MCNElasticSearch\Options\TypeMappingOptions[]
     */
    public function getAllTypeMappings();
}
