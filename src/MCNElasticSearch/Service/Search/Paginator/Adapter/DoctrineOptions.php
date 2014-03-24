<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service\Search\Paginator\Adapter;

use Zend\Stdlib\AbstractOptions;

/**
 * Class DoctrineOptions
 */
class DoctrineOptions extends AbstractOptions
{
    const STRATEGY_ONE_BY_ONE  = Doctrine\OneByOneStrategy::class;
    const STRATEGY_CRITERIA_IN = Doctrine\CriteriaInStrategy::class;

    protected $strategy = self::STRATEGY_CRITERIA_IN;

    /**
     * @param mixed $strategy
     */
    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @return mixed
     */
    public function getStrategy()
    {
        return $this->strategy;
    }
}
