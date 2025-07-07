<?php

namespace OpenLoyalty\Domain\Repository\Account;

use OpenLoyalty\Domain\Account\ReadModel\AccountDetails;
use OpenLoyalty\Domain\Repository\OloyElasticsearchRepository;

/**
 * Class AccountDetailsElasticsearchRepository.
 */
class AccountDetailsElasticsearchRepository extends OloyElasticsearchRepository
{
    /**
     * @param string $id
     * @return AccountDetails|null
     */
    public function find($id): ?AccountDetails
    {
        $result = parent::find($id);
        return $result instanceof AccountDetails ? $result : null;
    }

    /**
     * @param string $customerId
     * @return AccountDetails|null
     */
    public function findByCustomerId(string $customerId): ?AccountDetails
    {
        $result = $this->findBy(['customerId' => $customerId]);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * @param AccountDetails $accountDetails
     */
    public function save($accountDetails)
    {
        parent::save($accountDetails);
    }

    /**
     * @param mixed $readModel
     */
    public function saveGeneric($readModel)
    {
        if ($readModel instanceof AccountDetails) {
            $this->save($readModel);
        } else {
            parent::save($readModel);
        }
    }

    /**
     * @param string $id
     */
    public function remove($id)
    {
        parent::remove($id);
    }

    /**
     * @return AccountDetails[]
     */
    public function findAll(): array
    {
        return parent::findAll();
    }
} 