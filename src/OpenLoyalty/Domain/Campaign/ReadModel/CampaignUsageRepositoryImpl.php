<?php

namespace OpenLoyalty\Domain\Campaign\ReadModel;

/**
 * Simple implementation of CampaignUsageRepository
 */
class CampaignUsageRepositoryImpl implements CampaignUsageRepository
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
} 