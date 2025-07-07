<?php

namespace OpenLoyalty\Bundle\UserBundle\Service;

use OpenLoyalty\Domain\Customer\ReadModel\InvitationDetailsRepository;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerId;
use Broadway\ReadModel\RepositoryInterface;

/**
 * Mock repository for InvitationDetailsRepository
 */
class MockInvitationDetailsRepository implements InvitationDetailsRepository, RepositoryInterface
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

    public function findByToken($token)
    {
        return null;
    }

    public function findOneByRecipientId(CustomerId $recipient)
    {
        return null;
    }
} 