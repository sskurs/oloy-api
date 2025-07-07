<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\AnalyticsBundle\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetailsRepository;
use OpenLoyalty\Domain\Customer\Invitation;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Customer\ReadModel\InvitationDetailsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AnalyticsController.
 */
class AnalyticsController extends AbstractFOSRestController
{
    /**
     * Get transactions statistics.
     *
     * Method will return information about total transactions count, total amount, etc.
     *
     * [Example response] <br/>
     * <pre>{ <br/>
     *      "total": 10,<br/>
     *      "amount": 1200,<br/>
     *      "amountWithoutDeliveryCosts": 1000,<br/>
     *      "currency": "PLN"<br/>
     * }</pre>
     *
     * @Route(name="oloy.analytics.transactions", path="/admin/analytics/transactions", methods={"GET"})
     * @Security("is_granted('VIEW_STATS')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getTransactionsStats()
    {
        /** @var CustomerDetailsRepository $repo */
        $repo = $this->container->get('oloy.user.read_model.repository.customer_details');
        $currency = $this->container->get('ol.settings.manager')->getSettingByKey('currency');

        return $this->view([
            'total' => $repo->sumAllByField('transactionsCount'),
            'amount' => $repo->sumAllByField('transactionsAmount'),
            'amountWithoutDeliveryCosts' => $repo->sumAllByField('transactionsAmountWithoutDeliveryCosts'),
            'currency' => $currency ? $currency->getValue() : 'PLN',
        ]);
    }

    /**
     * Get points statistics.
     *
     * Method will return information about total count of spending points operations and total amount of used points.
     *
     * [Example response] <br/>
     * <pre>{ <br/>
     *      "totalSpendingTransfers": 11, // count of spending points operations<br/>
     *      "totalPointsSpent": 100 // total amount of used points<br/>
     * }</pre>
     *
     * @Route(name="oloy.analytics.points", path="/admin/analytics/points", methods={"GET"})
     * @Security("is_granted('VIEW_STATS')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getPointsStats()
    {
        /** @var PointsTransferDetailsRepository $repo */
        $repo = $this->container->get('oloy.points.account.repository.points_transfer_details');

        return $this->view([
            'totalSpendingTransfers' => $repo->countTotalSpendingTransfers(),
            'totalPointsSpent' => $repo->getTotalValueOfSpendingTransfers(),
        ]);
    }

    /**
     * Get customers statistics.
     *
     * Method will return information about total count of customers registered in system.
     *
     * [Example response] <br/>
     * <pre>{ <br/>
     *      "total": 11
     * }</pre>
     *
     * @Route(name="oloy.analytics.customers", path="/admin/analytics/customers", methods={"GET"})
     * @Security("is_granted('VIEW_STATS')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getCustomersStats()
    {
        /** @var CustomerDetailsRepository $repo */
        $repo = $this->container->get('oloy.user.read_model.repository.customer_details');

        return $this->view([
            'total' => $repo->countTotal(),
        ]);
    }

    /**
     * @Route(name="oloy.analytics.referrals", path="/admin/analytics/referrals", methods={"GET"})
     * @Security("is_granted('VIEW_STATS')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getReferralStats()
    {
        /** @var InvitationDetailsRepository $repo */
        $repo = $this->container->get('oloy.user.read_model.repository.invitation_details');

        $totalCompleted = $repo->countTotal(['status' => Invitation::STATUS_MADE_PURCHASE], true);
        $totalRegistered = $repo->countTotal(['status' => Invitation::STATUS_REGISTERED], true);

        return $this->view([
            'total' => $repo->countTotal(),
            'totalCompleted' => $totalCompleted,
            'totalRegistered' => $totalRegistered,
        ]);
    }
}
