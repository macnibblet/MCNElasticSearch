<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service;
use Elastica\Client;
use Elastica\Type\Mapping;
use Elastica\Type;
use MCNElasticSearch\Options\TypeMappingOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\Logger;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class MappingService
 */
class MappingService implements MappingServiceInterface
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
     * @param \Elastica\Client $client
     * @param MetadataService $metadata
     */
    public function __construct(Client $client, MetadataService $metadata)
    {
        $this->client   = $client;
        $this->metadata = $metadata;
    }

    public function build(array $types = [])
    {
        $mappings = $this->metadata->getAllTypeMappings();

        if ($types !== null) {
            array_filter($mappings, function(TypeMappingOptions $t) use ($types) {
                return in_array($t->getName(), $types);
            });
        }

        $hydrator = new ClassMethods();

        /** @var $options \MCNElasticSearch\Options\TypeMappingOptions */
        foreach ($mappings as $options) {

            $type = $this->client->getIndex($options->getIndex())
                                 ->getType($options->getName());

            $mapping = new Mapping($type);
            $hydrator->hydrate($options->toArray(), $mapping);

            $response = $mapping->send();
            if (! $response->isOk()) {
                $this->getEventManager()->trigger(__FUNCTION__ . '.error', $this, [ 'response' => $response ]);
                throw new Exception\RuntimeException(
                    sprintf(
                        'Error updating "%s" mapping: %s', $options->getName(), $response->getError()
                    )
                );
            }
        }
    }

    public function delete(array $types = [])
    {
        $mappings = $this->metadata->getAllTypeMappings();

        if ($types !== null) {
            array_filter($mappings, function(TypeMappingOptions $t) use ($types) {
                return in_array($t->getName(), $types);
            });
        }

        foreach ($mappings as $options) {

            // Delete the type
            $response = $this->client->getIndex($options->getIndex())->getType($options->getName())->delete();

            if (! $response->isOk()) {
                $this->getEventManager()->trigger(__FUNCTION__ . '.error', $this, [ 'response' => $response ]);
                throw new Exception\RuntimeException(
                    sprintf(
                        'Error deleting "%s" mapping: %s', $options->getName(), $response->getError()
                    )
                );
            }
        }
    }
}
