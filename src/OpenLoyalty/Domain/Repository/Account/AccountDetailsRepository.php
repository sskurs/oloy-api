<?php

namespace OpenLoyalty\Domain\Repository\Account;

use OpenLoyalty\Domain\Account\ReadModel\AccountDetails;

/**
 * Interface AccountDetailsRepository
 */
interface AccountDetailsRepository
{
    /**
     * @param string $id
     * @return AccountDetails|null
     */
    public function find(string $id): ?AccountDetails;

    /**
     * @param string $customerId
     * @return AccountDetails|null
     */
    public function findByCustomerId(string $customerId): ?AccountDetails;

    /**
     * @param AccountDetails $accountDetails
     */
    public function save(AccountDetails $accountDetails): void;

    /**
     * @param string $id
     */
    public function remove(string $id): void;

    /**
     * @return AccountDetails[]
     */
    public function findAll(): array;
} 