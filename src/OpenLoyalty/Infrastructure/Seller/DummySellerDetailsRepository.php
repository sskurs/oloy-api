<?php

namespace OpenLoyalty\Infrastructure\Seller;

use OpenLoyalty\Domain\Seller\ReadModel\SellerDetails;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetailsRepository;

/**
 * Dummy implementation of SellerDetailsRepository for development/testing
 * This avoids the need for Elasticsearch while allowing the application to run
 */
class DummySellerDetailsRepository implements SellerDetailsRepository
{
    public function save($readModel)
    {
        // Do nothing - this is a dummy implementation
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
        // Do nothing - this is a dummy implementation
    }

    public function findByParameters(array $params, $exact = true)
    {
        return [];
    }

    public function findByParametersPaginated(array $params, $exact = true, $page = 1, $perPage = 10, $sortField = null, $direction = 'DESC')
    {
        return [
            'sellers' => [],
            'total' => 0,
        ];
    }

    public function countTotal(array $params = [], $exact = true)
    {
        return 0;
    }
} 