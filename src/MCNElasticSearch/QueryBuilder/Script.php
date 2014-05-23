<?php
/**
 * @author Antoine Hedgcock
 */

namespace MCNElasticSearch\QueryBuilder;

class Script implements ExpressionInterface
{
    /**
     * @var string
     */
    private $script;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param string $script
     * @param array  $parameters
     */
    public function __construct($script, array $parameters = [])
    {
        $this->script     = $script;
        $this->parameters = $parameters;
    }

    public function toArray()
    {
        return ['script', [
            'script' => $this->script,
            'params' => $this->parameters
        ]];
    }
}
