<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service\Search\PaginatorAdapter;


use Elastica\Query;
use Elastica\SearchableInterface;
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
    protected $searchable;

    /**
     * @var int
     */
    protected $count;

    /**
     * @param SearchableInterface $searchable
     */
    public function setSearchable(SearchableInterface $searchable)
    {
        $this->searchable = $searchable;
    }

    /**
     * @param Query $query
     */
    public function setQuery(Query $query)
    {
        $this->query = $query;
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
            $this->count = $this->searchable->count($this->query);
        }

        return $this->count;
    }
}
