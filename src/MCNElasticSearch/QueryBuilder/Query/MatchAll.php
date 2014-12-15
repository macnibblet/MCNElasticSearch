<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Query;

class MatchAll implements QueryInterface
{
    public function toArray()
    {
        return ['match_all', [

        ]];
    }
}
