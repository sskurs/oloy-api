<?php

namespace OpenLoyalty\Bundle\UserBundle\Service;

use OpenLoyalty\Domain\Repository\Account\AccountDetailsRepositoryImpl;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetailsRepositoryImpl;
use OpenLoyalty\Domain\Repository\Campaign\CampaignRepository;
use OpenLoyalty\Domain\Repository\Customer\CustomerDetailsRepository;
use OpenLoyalty\Domain\Repository\Customer\InvitationDetailsRepository;
use OpenLoyalty\Domain\Repository\Customer\SellerDetailsRepository;
use OpenLoyalty\Domain\Repository\Segment\SegmentRepository;
use OpenLoyalty\Domain\Repository\Transaction\TransactionDetailsRepository;
use OpenLoyalty\Domain\Campaign\ReadModel\CouponUsageRepositoryImpl;
use OpenLoyalty\Domain\Campaign\ReadModel\CampaignUsageRepositoryImpl;
use OpenLoyalty\Bundle\UserBundle\Service\MockAccountDetailsRepository;
use OpenLoyalty\Bundle\UserBundle\Service\MockCustomerDetailsRepository;
use OpenLoyalty\Bundle\UserBundle\Service\MockCouponUsageRepository;
use OpenLoyalty\Bundle\UserBundle\Service\MockCampaignUsageRepository;

/**
 * Repository factory to create repository instances
 */
class RepositoryFactory
{
    public function create(string $repositoryType, string $modelClass, string $repositoryClass)
    {
        // Return appropriate repository based on type
        switch ($repositoryType) {
            case 'oloy.account_details':
                return new MockAccountDetailsRepository();
            case 'oloy.points_transfer_details':
                return new MockAccountDetailsRepository();
            case 'oloy.customer_details':
                return new MockCustomerDetailsRepository();
            case 'oloy.invitation_details':
                return new MockCustomerDetailsRepository();
            case 'oloy.seller_details':
                return new MockCustomerDetailsRepository();
            case 'oloy.customers_belonging_to_one_level':
                return new MockCustomerDetailsRepository();
            case 'oloy.transaction_details':
                return new MockCustomerDetailsRepository();
            case 'oloy.segmented_customers':
                return new MockCustomerDetailsRepository();
            case 'oloy.coupon_usage':
                return new MockCouponUsageRepository();
            case 'oloy.campaign_usage':
                return new MockCampaignUsageRepository();
            default:
                // Return a mock repository for unknown types
                return new MockCustomerDetailsRepository();
        }
    }
} 