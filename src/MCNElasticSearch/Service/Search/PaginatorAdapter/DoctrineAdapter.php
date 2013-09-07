<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service\Search\PaginatorAdapter;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;
use Elastica\Result;
use MCNElasticSearch\Options\ObjectMetadataOptions;
use MCNElasticSearch\Service\Exception;

/**
 * Class DoctrineAdapter
 */
class DoctrineAdapter extends AbstractAdapter
{
    /**
     * @var \Doctrine\Common\Collections\Selectable
     */
    protected $selectable;

    /**
     * @var array
     */
    protected $preservedKeys = ['sort'];

    /**
     * @var \MCNElasticSearch\Options\ObjectMetadataOptions
     */
    protected $objectMetadata;

    /**
     * @param Selectable $selectable
     */
    public function setRepository(Selectable $selectable)
    {
        $this->selectable = $selectable;
    }

    /**
     * @param ObjectMetadataOptions $objectMetadata
     */
    public function setObjectMetadata(ObjectMetadataOptions $objectMetadata)
    {
        $this->objectMetadata = $objectMetadata;
    }

    /**
     * @param Result $object
     *
     * @return mixed
     */
    public function hydrate(Result $object)
    {
        throw new Exception\LogicException('Should never be called');
    }

    /**
     * @param int $offset
     * @param int $itemCountPerPage
     *
     * @return array|\Doctrine\Common\Collections\Collection
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->query->setFrom($offset);
        $this->query->setSize($itemCountPerPage);

        $data    = [];
        $results = $this->doRequest()->getResults();

        foreach ($results as $result) {

            $tmp = [];
            foreach ($this->preservedKeys as $key) {
                $tmp[$key] = $result->getParam($key);
            }

            $data[$result->getId()] = $tmp;
        }

        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->in(
                $this->objectMetadata->getId(),
                array_keys($data)
            )
       );

        $items  = $this->selectable->matching($criteria);
        $return = [];

        foreach ($items as $item) {

            $id = $item[$this->objectMetadata->getId()];

            if (! empty($data[$id])) {
                $return[] = [$item] + $data[$id];
            } else {
                $return[] = $item;
            }
        }

        return $return;
    }
}
