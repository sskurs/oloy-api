<?php

namespace OpenLoyalty\Bundle\UserBundle\Service;

use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Customer\CustomerId;
use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;

/**
 * Mock repository for CustomerDetailsRepository
 */
class MockCustomerDetailsRepository implements CustomerDetailsRepository, RepositoryInterface
{
    public function save($readModel)
    {
        return $readModel;
    }

    public function find($id)
    {
        return null;
    }

    public function findBy(array $fields)
    {
        return [];
    }

    public function findAll()
    {
        return [];
    }

    public function remove($id)
    {
        return true;
    }

    public function findByBirthdayAnniversary(\DateTime $from, \DateTime $to, $onlyActive = true)
    {
        return [];
    }

    public function findByCreationAnniversary(\DateTime $from, \DateTime $to, $onlyActive = true)
    {
        return [];
    }

    public function findByParameters(array $params, $exact = true)
    {
        return [];
    }

    public function findByParametersPaginated(array $params, $exact = true, $page = 1, $perPage = 10, $sortField = null, $direction = 'DESC')
    {
        return [];
    }

    public function countTotal(array $params = [], $exact = true)
    {
        return 0;
    }

    public function findPurchasesByCustomerIdPaginated(CustomerId $customerId, $page = 1, $perPage = 10, $sortField = null, $direction = 'DESC')
    {
        return [];
    }

    public function countPurchasesByCustomerId(CustomerId $customerId)
    {
        return 0;
    }

    public function findOneByCriteria($criteria, $limit)
    {
        return null;
    }

    public function findAllWithAverageTransactionAmountBetween($from, $to, $onlyActive = true)
    {
        return [];
    }

    public function findAllWithTransactionAmountBetween($from, $to, $onlyActive = true)
    {
        return [];
    }

    public function findAllWithTransactionCountBetween($from, $to, $onlyActive = true)
    {
        return [];
    }

    public function sumAllByField($fieldName)
    {
        return 0;
    }
} 