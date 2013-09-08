<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Elastica\Client;
use Elastica\Query;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\ProvidesEvents;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\HydratorPluginManager;

/**
 * Class SearchService
 */
class SearchService implements SearchServiceInterface
{
    use EventManagerAwareTrait;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @var MetadataService
     */
    protected $metadata;

    /**
     * @var \Elastica\Client
     */
    protected $client;

    /**
     * @param \Elastica\Client $client
     * @param MetadataService $metadata
     * @param ObjectManager $objectManager
     */
    public function __construct(Client $client, MetadataService $metadata, ObjectManager $objectManager)
    {
        $this->client        = $client;
        $this->metadata      = $metadata;
        $this->objectManager = $objectManager;
    }

    /**
     * Perform a search
     *
     * @param string $objectClassName
     * @param \Elastica\Query $query
     * @param string $hydration
     *
     * @throws Exception\InvalidArgumentException
     * @return \Zend\Paginator\Paginator
     */
    public function search($objectClassName, Query $query, $hydration = self::HYDRATE_RAW)
    {
        $metadata = $this->metadata->getObjectMetadata($objectClassName);
        $type     = $this->client->getIndex($metadata->getIndex())
                                 ->getType($metadata->getType());

        switch ($hydration)
        {
            case static::HYDRATE_DOCTRINE_OBJECT:
                $adapter = new Search\PaginatorAdapter\Doctrine(
                    $this->objectManager->getRepository($objectClassName),
                    $metadata
                );
                break;

            case static::HYDRATE_RAW:
                $adapter = new Search\PaginatorAdapter\Raw();
                break;

            default:
                throw new Exception\InvalidArgumentException(sprintf('Unknown hydration mode %s', $hydration));
        }

        $adapter->setQuery($query);
        $adapter->setSearchable($type);
        return new Paginator($adapter);
    }
}
