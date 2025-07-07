<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Transaction\ReadModel;

interface TransactionDetailsRepository
{
    public function save($readModel);

    public function find($id);

    public function findBy(array $fields);

    public function findAll();

    public function remove($id);

    public function findInPeriod(\DateTime $from, \DateTime $to, $onlyWithCustomers = true);

    public function findAllWithCustomer();

    public function findBySKUs(array $skuIds, $withCustomer = true);

    public function findByMakers(array $makers, $withCustomer = true);

    public function findByLabels(array $labels, $withCustomer = true);

    public function getAvailableLabels();

    public function findByParameters(array $params, $exact = true);

    public function findByParametersPaginated(array $params, $exact = true, $page = 1, $perPage = 10, $sortField = null, $direction = 'DESC');

    public function countTotal(array $params = [], $exact = true);
}
