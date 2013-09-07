<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service;

use Elastica\Document;
use Zend\Log\Logger;
use Zend\Stdlib\Hydrator\HydratorPluginManager;

/**
 * Class DocumentService
 */
class DocumentService implements DocumentServiceInterface
{
    /**
     * @var \Elastica\Client
     */
    protected $client;

    /**
     * @var ConfigurationService
     */
    protected $config;

    /**
     * @var \Zend\Log\Logger
     */
    protected $logger;

    /**
     * @var \Zend\Stdlib\Hydrator\HydratorPluginManager
     */
    protected $hydratorManager;

    /**
     * @param ConfigurationService $config
     * @param \Zend\Stdlib\Hydrator\HydratorPluginManager $hydratorManager
     * @param \Zend\Log\Logger $logger
     */
    public function __construct(ConfigurationService $config, HydratorPluginManager $hydratorManager, Logger $logger)
    {
        $this->config          = $config;
        $this->logger          = $logger;
        $this->client          = $config->getClient();
        $this->hydratorManager = $hydratorManager;
    }

    /**
     * Converts an object into a elastica document
     *
     * @param mixed $object
     * @throws Exception\InvalidArgumentException If an invalid object is passed
     * @return \Elastica\Document
     */
    protected function transform($object)
    {
        if (! is_object($object)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Object of type %s is invalid; Must be a valid class',
                (is_object($object) ? get_class($object) : gettype($object))
            ));
        }

        /** @var \Zend\Stdlib\Hydrator\AbstractHydrator $hydrator */
        $metadata = $this->config->getObjectMetadata(get_class($object));
        $hydrator = $this->hydratorManager->get($metadata->getHydrator());

        // extract data
        $data = $hydrator->extract($object);

        // transform it to a document
        return new Document($data[$metadata->getId()], $data, $metadata->getType(), $metadata->getIndex());
    }

    /**
     * Add a document
     *
     * @param mixed $object
     *
     * @throws Exception\InvalidArgumentException   If an invalid object is passed
     * @throws Exception\RuntimeException           In case something goes wrong during persisting the document
     *
     * @return void
     */
    public function add($object)
    {
        $document = $this->transform($object);
        $response = $this->client->addDocuments([$document]);

        if (! $response->isOk()) {
            $message = sprintf('Error adding document: %s', $response->getError());
            $this->logger->err($message, ['document' => $document]);
            throw new Exception\RuntimeException($message);
        }
    }

    /**
     * @param $object
     * @throws Exception\RuntimeException
     * @return void
     */
    public function update($object)
    {
        $document = $this->transform($object);
        $response = $this->client->updateDocument(
            $document->getId(),
            $document->getData(),
            $document->getIndex(),
            $document->getType()
       );

        if (! $response->isOk()) {
            $message = sprintf('Error updating document: %s', $response->getError());
            $this->logger->err($message, ['document' => $document]);
            throw new Exception\RuntimeException($message);
        }
    }

    /**
     * @param $object
     * @throws Exception\RuntimeException
     * @return mixed
     */
    public function delete($object)
    {
        $document = $this->transform($object);
        $response = $this->client->deleteDocuments([$document]);

        if (! $response->isOk()) {
            $message = sprintf('Error deleting document: %s', $response->getError());
            $this->logger->err($message, ['document' => $document]);
            throw new Exception\RuntimeException($message);
        }
    }
}
