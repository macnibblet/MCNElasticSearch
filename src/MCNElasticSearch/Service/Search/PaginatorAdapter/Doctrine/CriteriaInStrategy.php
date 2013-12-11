<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service\Search\PaginatorAdapter\Doctrine;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;
use MCNElasticSearch\Service\Exception;
use MCNElasticSearch\Options\MetadataOptions;

/**
 * Class CriteriaInStrategy
 */
class CriteriaInStrategy implements LoaderStrategyInterface
{
    /**
     * Load a bunch of items from the object repository
     *
     * @param array                 $items
     * @param ObjectRepository      $repository
     * @param MetadataOptions $objectMetadata
     *
     * @throws \MCNElasticSearch\Service\Exception\InvalidArgumentException If an invalid repository has been specified.
     *
     * @return Collection
     */
    public function load(array $items, ObjectRepository $repository, MetadataOptions $objectMetadata)
    {
        if (! $repository instanceof Selectable) {
            throw new Exception\InvalidArgumentException(
                sprintf('%s requires a repository that implements the %s interface.', __METHOD__, Selectable::class)
            );
        }

        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->in($objectMetadata->getId(), $items)
        );

        return $repository->matching($criteria);
    }
}
