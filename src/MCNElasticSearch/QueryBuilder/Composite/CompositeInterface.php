<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder\Composite;

use MCNElasticSearch\QueryBuilder\ExpressionInterface;

interface CompositeInterface extends ExpressionInterface
{
    public function isEmpty();
}
