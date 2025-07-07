<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\ReadModel;

interface InvitationDetailsRepository
{
    public function save($readModel);

    public function find($id);

    public function findBy(array $fields);

    public function findAll();

    public function remove($id);

    public function findByParameters(array $params, $exact = true);

    public function findByParametersPaginated(array $params, $exact = true, $page = 1, $perPage = 10, $sortField = null, $direction = 'DESC');

    public function countTotal(array $params = [], $exact = true);

    public function findByToken($token);

    public function findOneByRecipientId(CustomerId $recipient);
}
