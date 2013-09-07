<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Controller;

use Zend\Console\Prompt;
use MCNElasticSearch\Service\MappingServiceInterface;

/**
 * Class MappingController
 */
class MappingController extends AbstractCliController
{
    /**
     * @var \MCNElasticSearch\Service\MappingServiceInterface
     */
    protected $service;

    /**
     * @param MappingServiceInterface $service
     */
    public function __construct(MappingServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Build the schema
     */
    public function buildMappingAction()
    {
        $this->service->build();
    }

    /**
     * Delete the entire mapping
     */
    public function deleteMappingAction()
    {
        $skipPrompt = $this->getRequest()->getParam('y', false);

        if (! $skipPrompt) {
            $prompt = new Prompt\Confirm('Are you sure you want to delete everything ?');
            $prompt->show();

            if (!$prompt) {
                return;
            }
        }

        $this->service->delete();
    }
}
