<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Seller\Command;

use OpenLoyalty\Domain\Seller\SellerId;

/**
 * Class SellerCommand.
 */
abstract class SellerCommand
{
    /**
     * @var SellerId
     */
    protected $sellerId;

    /**
     * SellerCommand constructor.
     *
     * @param SellerId $sellerId
     */
    public function __construct(SellerId $sellerId)
    {
        $this->sellerId = $sellerId;
    }

    /**
     * @return SellerId
     */
    public function getSellerId()
    {
        return $this->sellerId;
    }
}
