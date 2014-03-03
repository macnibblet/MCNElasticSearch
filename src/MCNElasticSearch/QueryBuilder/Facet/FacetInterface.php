<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Facet;

use MCNElasticSearch\QueryBuilder\ExpressionInterface;

interface FacetInterface extends ExpressionInterface
{
    public function getName();
}
