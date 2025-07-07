<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Campaign\ReadModel;

use OpenLoyalty\Domain\Campaign\CampaignId;
use OpenLoyalty\Domain\Campaign\CustomerId;

interface CouponUsageRepository
{
    public function save($readModel);

    public function find($id);

    public function findBy(array $fields);

    public function findAll();

    public function remove($id);

    public function findByParameters(array $params, $exact = true);

    public function findByParametersPaginated(array $params, $exact = true, $page = 1, $perPage = 10, $sortField = null, $direction = 'DESC');

    public function countTotal(array $params = [], $exact = true);

    public function countUsageForCampaign(CampaignId $campaignId);

    public function countUsageForCampaignAndCustomer(CampaignId $campaignId, CustomerId $customerId);

    public function findByCampaign(CampaignId $campaignId);
}
