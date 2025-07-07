<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Account\ReadModel;

interface AccountDetailsRepository
{
    public function save($readModel);

    public function find($id);

    public function findBy(array $fields);

    public function findAll();

    public function remove($id);
} 