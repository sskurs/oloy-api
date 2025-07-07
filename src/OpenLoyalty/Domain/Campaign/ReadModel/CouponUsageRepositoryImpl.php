<?php

namespace OpenLoyalty\Domain\Campaign\ReadModel;

use OpenLoyalty\Domain\Campaign\CampaignId;
use OpenLoyalty\Domain\Campaign\CustomerId;

/**
 * Simple implementation of CouponUsageRepository
 */
class CouponUsageRepositoryImpl implements CouponUsageRepository
{
    /**
     * @var array
     */
    private $usages = [];

    public function save($readModel)
    {
        $this->usages[$readModel->getId()] = $readModel;
    }

    public function find($id)
    {
        return $this->usages[$id] ?? null;
    }

    public function findBy(array $fields)
    {
        // Simple implementation - return all usages
        return array_values($this->usages);
    }

    public function findAll()
    {
        return array_values($this->usages);
    }

    public function remove($id)
    {
        unset($this->usages[$id]);
    }

    public function findByParameters(array $params, $exact = true)
    {
        // Simple implementation - return all usages
        return array_values($this->usages);
    }

    public function findByParametersPaginated(array $params, $exact = true, $page = 1, $perPage = 10, $sortField = null, $direction = 'DESC')
    {
        // Simple implementation - return all usages
        return array_values($this->usages);
    }

    public function countTotal(array $params = [], $exact = true)
    {
        return count($this->usages);
    }

    public function countUsageForCampaign(CampaignId $campaignId)
    {
        $count = 0;
        foreach ($this->usages as $usage) {
            if ($usage->getCampaignId() == $campaignId) {
                $count++;
            }
        }
        return $count;
    }

    public function countUsageForCampaignAndCustomer(CampaignId $campaignId, CustomerId $customerId)
    {
        $count = 0;
        foreach ($this->usages as $usage) {
            if ($usage->getCampaignId() == $campaignId && $usage->getCustomerId() == $customerId) {
                $count++;
            }
        }
        return $count;
    }

    public function findByCampaign(CampaignId $campaignId)
    {
        $result = [];
        foreach ($this->usages as $usage) {
            if ($usage->getCampaignId() == $campaignId) {
                $result[] = $usage;
            }
        }
        return $result;
    }
} 