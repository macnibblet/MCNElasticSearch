<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service\Search\PaginatorAdapter\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;
use MCNElasticSearch\Options\ObjectMetadataOptions;

/**
 * Class OneByOneStrategy
 */
class OneByOneStrategy implements LoaderStrategyInterface
{
    /**
     * Load a bunch of items from the object repository
     *
     * @param array                 $items
     * @param ObjectRepository      $repository
     * @param ObjectMetadataOptions $objectMetadata
     *
     * @return Collection
     */
    public function load(array $items, ObjectRepository $repository, ObjectMetadataOptions $objectMetadata)
    {
        $collection = new ArrayCollection();

        foreach ($items as $item) {

            $result = $repository->findOneBy([$objectMetadata->getId() => $item]);

            // not doing this could cause some strange issues!
            if ($result) {
                $collection->add($result);
            }
        }

        return $collection;
    }
}
