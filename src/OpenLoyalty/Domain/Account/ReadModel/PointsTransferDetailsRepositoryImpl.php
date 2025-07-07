<?php

namespace OpenLoyalty\Domain\Account\ReadModel;

/**
 * Simple implementation of PointsTransferDetailsRepository
 */
class PointsTransferDetailsRepositoryImpl implements PointsTransferDetailsRepository
{
    /**
     * @var array
     */
    private $transfers = [];

    public function save($readModel)
    {
        $this->transfers[$readModel->getId()] = $readModel;
    }

    public function find($id)
    {
        return $this->transfers[$id] ?? null;
    }

    public function findBy(array $fields)
    {
        // Simple implementation - return all transfers
        return array_values($this->transfers);
    }

    public function findAll()
    {
        return array_values($this->transfers);
    }

    public function remove($id)
    {
        unset($this->transfers[$id]);
    }

    public function findByParameters(array $params, $exact = true)
    {
        // Simple implementation - return all transfers
        return array_values($this->transfers);
    }

    public function findByParametersPaginated(array $params, $exact = true, $page = 1, $perPage = 10, $sortField = null, $direction = 'DESC')
    {
        // Simple implementation - return all transfers
        return array_values($this->transfers);
    }

    public function countTotal(array $params = [], $exact = true)
    {
        return count($this->transfers);
    }

    public function findAllActiveAddingTransfersCreatedAfter($timestamp)
    {
        // Simple implementation - return all transfers
        return array_values($this->transfers);
    }

    public function findAllPaginated($page = 1, $perPage = 10, $sortField = 'earningRuleId', $direction = 'DESC')
    {
        // Simple implementation - return all transfers
        return array_values($this->transfers);
    }

    public function countTotalSpendingTransfers()
    {
        return 0;
    }

    public function getTotalValueOfSpendingTransfers()
    {
        return 0;
    }
} 