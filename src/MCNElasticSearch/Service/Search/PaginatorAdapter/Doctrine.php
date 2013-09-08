<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service\Search\PaginatorAdapter;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Elastica\Query;
use Elastica\SearchableInterface;
use MCNElasticSearch\Options\ObjectMetadataOptions;
use MCNElasticSearch\Service\Exception;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Class Doctrine
 */
class Doctrine extends AbstractAdapter
{
    /**
     * @var \Doctrine\Common\Collections\Selectable
     */
    protected $repository;

    /**
     * @var \MCNElasticSearch\Options\ObjectMetadataOptions
     */
    protected $objectMetadata;

    /**
     * @param \Doctrine\Common\Collections\Selectable         $repository
     * @param \MCNElasticSearch\Options\ObjectMetadataOptions $objectMetadata
     */
    public function __construct(Selectable $repository, ObjectMetadataOptions $objectMetadata)
    {
        $this->repository     = $repository;
        $this->objectMetadata = $objectMetadata;
    }

    /**
     * Returns an collection of items for a page.
     *
     * @param int $offset
     * @param int $itemCountPerPage
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->query->setFrom($offset);
        $this->query->setSize($itemCountPerPage);

        $dataSet = [];
        $results = $this->searchable->search($this->query);

        /** @var $result \Elastica\Result */
        foreach ($results as $result) {
            $data = [];
            foreach ($result->getHit() as $key => $value) {
                if (substr($key, 0, 1) != '_') {
                    $data[$key] = $value;
                }
            }

            $dataSet[$result->getId()] = $data;
        }

        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->in(
                $this->objectMetadata->getId(),
                array_keys($dataSet)
            )
       );

        $items  = $this->repository->matching($criteria);
        $return = [];

        foreach ($items as $item) {

            $id = $item[$this->objectMetadata->getId()];

            if (! empty($dataSet[$id])) {
                $return[] = [$item] + $dataSet[$id];
            } else {
                $return[] = $item;
            }
        }

        return $return;
    }
}
