<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service;

use Elastica\Client;
use Elastica\Document;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Stdlib\Hydrator\HydratorPluginManager;

/**
 * Class DocumentService
 */
class DocumentService implements DocumentServiceInterface
{
    use EventManagerAwareTrait;

    /**
     * @var \Elastica\Client
     */
    protected $client;

    /**
     * @var MetadataService
     */
    protected $metadata;

    /**
     * @var \Zend\Stdlib\Hydrator\HydratorPluginManager
     */
    protected $hydratorManager;

    /**
     * @param \Elastica\Client $client
     * @param MetadataService $metadata
     * @param \Zend\Stdlib\Hydrator\HydratorPluginManager $hydratorManager
     */
    public function __construct(Client $client, MetadataService $metadata, HydratorPluginManager $hydratorManager)
    {
        $this->client          = $client;
        $this->metadata        = $metadata;
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
        $metadata = $this->metadata->getObjectMetadata(get_class($object));
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
     * @triggers add.pre
     * @triggers add.post
     * @triggers add.error
     *
     * @throws Exception\InvalidArgumentException   If an invalid object is passed
     * @throws Exception\RuntimeException           In case something goes wrong during persisting the document
     *
     * @return void
     */
    public function add($object)
    {
        $document = $this->transform($object);

        $this->getEventManager()
             ->trigger(__FUNCTION__ . '.pre', $this, ['document' => $document, 'object' => $object]);

        $response = $this->client->addDocuments([$document]);

        $this->getEventManager()
             ->trigger(__FUNCTION__ . '.post', $this, ['document' => $document, 'object' => $object]);

        if (! $response->isOk()) {
            $this->getEventManager()
                 ->trigger(__FUNCTION__ . '.error', $this, ['response' => $response]);

            throw new Exception\RuntimeException($response->getError());
        }
    }

    /**
     * Update a document
     *
     * @param mixed $object
     *
     * @triggers update.pre
     * @triggers update.post
     * @triggers update.error
     *
     * @throws Exception\InvalidArgumentException If an invalid object is passed
     * @throws Exception\RuntimeException         In case something goes wrong during an update
     *
     * @return void
     */
    public function update($object)
    {
        $document = $this->transform($object);

        $this->getEventManager()
             ->trigger(__FUNCTION__ . '.pre', $this, ['document' => $document, 'object' => $object]);

        $response = $this->client->updateDocument(
            $document->getId(),
            $document->getData(),
            $document->getIndex(),
            $document->getType()
       );

        $this->getEventManager()
             ->trigger(__FUNCTION__ . '.post', $this, ['document' => $document, 'object' => $object]);

        if (! $response->isOk()) {
            $this->getEventManager()
                 ->trigger(__FUNCTION__ . '.error', $this, ['response' => $response]);

            throw new Exception\RuntimeException($response->getError());
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

        $this->getEventManager()
             ->trigger(__FUNCTION__ . '.pre', $this, ['document' => $document, 'object' => $object]);

        $response = $this->client->deleteDocuments([$document]);

        $this->getEventManager()
             ->trigger(__FUNCTION__ . '.post', $this, ['document' => $document, 'object' => $object]);

        if (! $response->isOk()) {
            $this->getEventManager()
                 ->trigger(__FUNCTION__ . '.error', $this, ['response' => $response]);

            throw new Exception\RuntimeException($response->getError());
        }
    }
}
