<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service\Search\Paginator\Adapter\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;
use MCNElasticSearch\Options\MetadataOptions;

/**
 * Class OneByOneStrategy
 */
class OneByOneStrategy implements LoaderStrategyInterface
{
    /**
     * Load a bunch of items from the object repository
     *
     * @param array            $items
     * @param ObjectRepository $repository
     * @param MetadataOptions  $metadata
     *
     * @return Collection
     */
    public function load(array $items, ObjectRepository $repository, MetadataOptions $metadata)
    {
        $collection = new ArrayCollection();

        foreach ($items as $item) {
            $result = $repository->findOneBy([$metadata->getId() => $item]);

            // not doing this could cause some strange issues!
            if ($result) {
                $collection->add($result);
            }
        }

        return $collection;
    }
}
