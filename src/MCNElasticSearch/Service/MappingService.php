<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service;
use Elastica\Type\Mapping;
use Elastica\Type;
use MCNElasticSearch\Options\TypeMappingOptions;
use Zend\Log\Logger;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class MappingService
 */
class MappingService implements MappingServiceInterface
{
    /**
     * @var ConfigurationService
     */
    protected $configuration;
    /**
     * @var \Zend\Log\Logger
     */
    private $logger;

    /**
     * @param ConfigurationService $configuration
     * @param \Zend\Log\Logger $logger
     */
    public function __construct(ConfigurationService $configuration, Logger $logger)
    {
        $this->logger        = $logger;
        $this->configuration = $configuration;
    }

    /**
     * @param array $types
     * @throws Exception\RuntimeException
     */
    public function build(array $types = null)
    {
        $client   = $this->configuration->getClient();
        $mappings = $this->configuration->getAllTypeMappings();

        if ($types !== null) {
            array_filter($mappings, function(TypeMappingOptions $t) use ($types) {
                return in_array($t->getName(), $types);
            });
        }

        $hydrator = new ClassMethods();

        /** @var $options \MCNElasticSearch\Options\TypeMappingOptions */
        foreach ($mappings as $options) {

            $type = $client->getIndex($options->getIndex())
                           ->getType($options->getName());

            $mapping = new Mapping($type);
            $hydrator->hydrate($options->toArray(), $mapping);

            $response = $mapping->send();
            if (! $response->isOk()) {
                $message = sprintf('Error updating "%s" mapping: %s', $options->getName(), $response->getError());
                $this->logger->err($message, ['mapping' => $mapping, 'options' => $options]);
                throw new Exception\RuntimeException($message);
            }
        }
    }

    public function delete(array $types = null)
    {
        $client   = $this->configuration->getClient();
        $mappings = $this->configuration->getAllTypeMappings();

        if ($types !== null) {
            array_filter($mappings, function(TypeMappingOptions $t) use ($types) {
                return in_array($t->getName(), $types);
            });
        }

        foreach ($mappings as $options) {

            // Delete the type
            $response = $client->getIndex($options->getIndex())->getType($options->getName())->delete();
            if (! $response->isOk()) {
                $message = sprintf('Error deleting "%s"', $options->getName());
                $this->logger->err($message, ['options' => $options]);
                throw new Exception\RuntimeException($message);
            }
        }
    }
}
