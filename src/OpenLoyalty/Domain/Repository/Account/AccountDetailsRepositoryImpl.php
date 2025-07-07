<?php

namespace OpenLoyalty\Domain\Repository\Account;

use OpenLoyalty\Domain\Account\ReadModel\AccountDetails;

/**
 * Simple implementation of AccountDetailsRepository
 */
class AccountDetailsRepositoryImpl implements AccountDetailsRepository
{
    /**
     * @var array
     */
    private $accounts = [];

    /**
     * @param string $id
     * @return AccountDetails|null
     */
    public function find(string $id): ?AccountDetails
    {
        return $this->accounts[$id] ?? null;
    }

    /**
     * @param string $customerId
     * @return AccountDetails|null
     */
    public function findByCustomerId(string $customerId): ?AccountDetails
    {
        foreach ($this->accounts as $account) {
            if ($account->getCustomerId() === $customerId) {
                return $account;
            }
        }
        return null;
    }

    /**
     * @param AccountDetails $accountDetails
     */
    public function save(AccountDetails $accountDetails): void
    {
        $this->accounts[$accountDetails->getId()] = $accountDetails;
    }

    /**
     * @param string $id
     */
    public function remove(string $id): void
    {
        unset($this->accounts[$id]);
    }

    /**
     * @return AccountDetails[]
     */
    public function findAll(): array
    {
        return array_values($this->accounts);
    }
} 