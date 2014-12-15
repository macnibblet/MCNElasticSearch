<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Query;

class QueryString implements QueryInterface
{
    /**
     * @var string
     */
    private $queryString;

    /**
     * @var array
     */
    private $options;

    public function __construct($queryString, array $options = [])
    {
        $this->options     = $options;
        $this->queryString = $queryString;
    }

    public function toArray()
    {
        return ['query_string', [
            'query' => $this->queryString
        ] + $this->options];
    }
}
