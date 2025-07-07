<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\PointsBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CustomerPointsTransferController.
 *
 * @Security("is_granted('ROLE_PARTICIPANT')")
 */
class CustomerPointsTransferController extends AbstractFOSRestController
{
    /**
     * List of all logged in customer points transfer.
     *
     * @Route(name="oloy.points.transfer.customer.list", path="/customer/points/transfer")
     * @Method("GET")
     * @Security("is_granted('LIST_CUSTOMER_POINTS_TRANSFERS')")
     *
     * @param Request      $request
     * @param ParamFetcher $paramFetcher
     *
     * @return \FOS\RestBundle\View\View
     * @QueryParam(name="state", nullable=true, requirements="[a-zA-Z0-9\-]+", description="state"))
     * @QueryParam(name="type", nullable=true, requirements="[a-zA-Z0-9\-]+", description="type"))
     */
    public function listAction(Request $request, ParamFetcher $paramFetcher)
    {
        $params = $this->get('oloy.user.param_manager')->stripNulls($paramFetcher->all(), true, false);
        $params['customerId'] = $this->getUser()->getId();
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request, 'createdAt', 'DESC');

        /** @var PointsTransferDetailsRepository $repo */
        $repo = $this->get('oloy.points.account.repository.points_transfer_details');

        $transfers = $repo->findByParametersPaginated(
            $params,
            false,
            $pagination->getPage(),
            $pagination->getPerPage(),
            $pagination->getSort(),
            $pagination->getSortDirection()
        );
        $total = $repo->countTotal($params, false);

        return $this->view([
            'transfers' => $transfers,
            'total' => $total,
        ], 200);
    }
}
