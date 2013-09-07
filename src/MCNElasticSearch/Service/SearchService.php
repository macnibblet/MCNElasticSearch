<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Elastica\Query;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\HydratorPluginManager;

/**
 * Class SearchService
 */
class SearchService implements SearchServiceInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @var ConfigurationService
     */
    protected  $configuration;

    /**
     * @param ConfigurationService $configuration
     * @param ObjectManager $objectManager
     */
    public function __construct(ConfigurationService $configuration, ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->configuration = $configuration;
    }

    /**
     * Perform a search
     *
     * @param string $objectClassName
     * @param \Elastica\Query $criteria
     * @param string $hydration
     *
     * @throws Exception\InvalidArgumentException
     * @return \Zend\Paginator\Paginator
     */
    public function search($objectClassName, Query $criteria, $hydration = self::HYDRATE_OBJECT)
    {
        $metadata = $this->configuration->getObjectMetadata($objectClassName);
        $client   = $this->configuration->getClient();

        $type = $client->getIndex($metadata->getIndex());

        switch ($hydration)
        {
            case static::HYDRATE_DOCTRINE_OBJECT:
                $adapter = new Search\PaginatorAdapter\DoctrineAdapter($type, $criteria);
                $adapter->setObjectMetadata($metadata);
                $adapter->setRepository($this->objectManager->getRepository($objectClassName));
                break;

            default:
                throw new Exception\InvalidArgumentException(sprintf('Unknown hydration mode %s', $hydration));
        }

        return new Paginator($adapter);
    }
}
