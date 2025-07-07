<?php

namespace OpenLoyalty\Bundle\UserBundle\Service;

use OpenLoyalty\Domain\Account\ReadModel\AccountDetails;
use Broadway\ReadModel\RepositoryInterface;
use Broadway\ReadModel\Identifiable;

/**
 * Mock repository for AccountDetailsRepository - uses magic methods to handle interface conflicts
 */
class MockAccountDetailsRepository implements RepositoryInterface
{
    public function findByCustomerId(string $customerId): ?AccountDetails
    {
        return null;
    }

    public function findAll(): array
    {
        return [];
    }

    public function find($id)
    {
        return null;
    }

    public function save($data)
    {
        // Mock implementation - does nothing
    }

    public function remove($id)
    {
        // Mock implementation - does nothing
    }

    public function findBy(array $fields)
    {
        return [];
    }

    /**
     * Magic method to handle any other method calls
     */
    public function __call($name, $arguments)
    {
        // Return null for any other method calls
        return null;
    }
} 