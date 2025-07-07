<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\EarningRuleBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class CustomerEarningRuleController.
 */
class CustomerEarningRuleController extends AbstractFOSRestController
{
    /**
     * Method will return all active earning rules.
     *
     * @Route(name="oloy.earning_rule.customer.list", path="/customer/earningRule")
     * @Method("GET")
     * @Security("is_granted('LIST_ACTIVE_EARNING_RULES')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getListAction()
    {
        $earningRuleRepository = $this->get('oloy.earning_rule.repository');
        $rules = $earningRuleRepository
            ->findAllActive();

        $currency = $this->get('ol.settings.manager')->getSettingByKey('currency');

        return $this->view(
            [
                'earningRules' => $rules,
                'currency' => $currency ? $currency->getValue() : 'PLN',
            ],
            200
        );
    }
}
