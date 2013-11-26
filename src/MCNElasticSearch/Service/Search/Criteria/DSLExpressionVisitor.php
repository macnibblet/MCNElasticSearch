<?php
/**
 * Copyright (c) 2011-2013 Antoine Hedgecock.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author      Antoine Hedgecock <antoine@pmg.se>
 * @author      Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright   2011-2013 Antoine Hedgecock
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace MCNElasticSearch\Service\Search\Criteria;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression as CommonCompositeExpression;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;

/**
 * Class DSLExpressionVisitor
 */
class DSLExpressionVisitor extends ExpressionVisitor
{
    /**
     * Converts a comparison expression into the target query language output.
     *
     * @param Comparison $comparison
     *
     * @return mixed
     */
    public function walkComparison(Comparison $comparison)
    {
        switch ($comparison->getOperator())
        {
            case Comparison::EQ:
                return ['term' => [$comparison->getField() => $comparison->getValue()]];
        }
    }

    /**
     * Converts a value expression into the target query language part.
     *
     * @param Value $value
     *
     * @return mixed
     */
    public function walkValue(Value $value)
    {
        // TODO: Implement walkValue() method.
    }

    /**
     * Converts a composite expression into the target query language output.
     *
     * @param CompositeExpression $expr
     *
     * @return mixed
     */
    public function walkCompositeExpression(CommonCompositeExpression $expr)
    {
        $children = array();

        foreach ($expr->getExpressionList() as $child) {
            $children[] = $this->dispatch($child);
        }

        switch ($expr->getType())
        {
            case CompositeExpression::TYPE_AND:
                return ['and' => $children];

            case CompositeExpression::TYPE_OR:
                return ['or' => $children];

            case CompositeExpression::TYPE_QUERY:
                return ['query' => $children];

            case CompositeExpression::TYPE_FILTERED:
                return ['filtered' => $children];
        }
    }
}
