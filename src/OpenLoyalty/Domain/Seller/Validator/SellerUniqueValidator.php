<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Seller\Validator;

use OpenLoyalty\Domain\Seller\Exception\EmailAlreadyExistsException;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetails;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetailsRepository;
use OpenLoyalty\Domain\Seller\SellerId;

/**
 * Class SellerUniqueValidator.
 */
class SellerUniqueValidator
{
    /**
     * @var SellerDetailsRepository
     */
    protected $sellerDetailsRepository;

    /**
     * CustomerUniqueValidator constructor.
     *
     * @param SellerDetailsRepository $customerDetailsRepository
     */
    public function __construct(SellerDetailsRepository $customerDetailsRepository)
    {
        $this->sellerDetailsRepository = $customerDetailsRepository;
    }

    public function validateEmailUnique($email, SellerId $sellerId = null)
    {
        $sellers = $this->sellerDetailsRepository->findBy(['email' => $email]);
        if ($sellerId) {
            /** @var SellerDetails $seller */
            foreach ($sellers as $key => $seller) {
                if ($seller->getId() == $sellerId->__toString()) {
                    unset($sellers[$key]);
                }
            }
        }

        if (count($sellers) > 0) {
            throw new EmailAlreadyExistsException();
        }
    }
}
