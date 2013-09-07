<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service\Search\PaginatorAdapter;

use Elastica\Query;
use Elastica\Result;
use Elastica\SearchableInterface;
use MCNElasticSearch\Service\Exception;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Class AbstractAdapter
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @var \Elastica\Query
     */
    protected $query;

    /**
     * @var \Elastica\SearchableInterface
     */
    protected $repository;

    /**
     * @var int
     */
    protected $count;

    /**
     * @param \Elastica\SearchableInterface $repository
     * @param \Elastica\Query               $query
     */
    public function __construct(SearchableInterface $repository, Query $query)
    {
        $this->query      = $query;
        $this->repository = $repository;
    }

    /**
     * @param Result $object
     * @return mixed
     */
    abstract public function hydrate(Result $object);

    /**
     * @throws \MCNElasticSearch\Service\Exception\RuntimeException
     * @return \Elastica\ResultSet
     */
    protected function doRequest()
    {
        $result = $this->repository->search($this->query);

        // todo fix this bug in isOk
        if (! $result->getResponse()->isOk()) {
            // throw new Exception\RuntimeException($result->getResponse()->getError());
        }

        return $result;
    }

    /**
     * Returns an collection of items for a page.
     *
     * @param  int $offset Page offset
     * @param  int $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->query->setSize($itemCountPerPage);
        $this->query->setFrom($offset);

        return array_map([$this, 'hydrate'], $this->doRequest()->getResults());
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        if ($this->count === null) {
            $this->count = $this->doRequest()->getTotalHits();
        }

        return $this->count;
    }
}
