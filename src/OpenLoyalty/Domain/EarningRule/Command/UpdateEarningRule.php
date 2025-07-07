<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\EarningRule\Command;

use OpenLoyalty\Domain\EarningRule\EarningRuleId;

/**
 * Class UpdateEarningRule.
 */
class UpdateEarningRule extends EarningRuleCommand
{
    /**
     * @var array
     */
    protected $earningRuleData = [];

    public function __construct(EarningRuleId $earningRuleId, $earningRuleData)
    {
        parent::__construct($earningRuleId);
        $this->earningRuleData = $earningRuleData;
    }

    /**
     * @return array
     */
    public function getEarningRuleData()
    {
        return $this->earningRuleData;
    }
}
