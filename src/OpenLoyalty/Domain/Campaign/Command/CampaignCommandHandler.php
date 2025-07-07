<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Campaign\Command;

use Broadway\CommandHandling\CommandHandler;
use OpenLoyalty\Domain\Campaign\Campaign;
use OpenLoyalty\Domain\Campaign\CampaignRepository;

/**
 * Class CampaignCommandHandler.
 */
class CampaignCommandHandler implements CommandHandler
{
    /**
     * @var CampaignRepository
     */
    protected $campaignRepository;

    /**
     * CampaignCommandHandler constructor.
     *
     * @param CampaignRepository $campaignRepository
     */
    public function __construct(CampaignRepository $campaignRepository)
    {
        $this->campaignRepository = $campaignRepository;
    }

    public function handleCreateCampaign(CreateCampaign $command)
    {
        $data = $command->getCampaignData();
        Campaign::validateRequiredData($data);
        $campaign = new Campaign($command->getCampaignId(), $data);
        $this->campaignRepository->save($campaign);
    }

    public function handleUpdateCampaign(UpdateCampaign $command)
    {
        $data = $command->getCampaignData();
        Campaign::validateRequiredData($data);
        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->byId($command->getCampaignId());
        $campaign->setFromArray($command->getCampaignData());

        $this->campaignRepository->save($campaign);
    }

    public function handleChangeCampaignState(ChangeCampaignState $command)
    {
        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->byId($command->getCampaignId());
        $campaign->setActive($command->getActive());

        $this->campaignRepository->save($campaign);
    }

    public function handleSetCampaignPhoto(SetCampaignPhoto $command)
    {
        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->byId($command->getCampaignId());
        $campaign->setCampaignPhoto($command->getCampaignPhoto());

        $this->campaignRepository->save($campaign);
    }

    public function handleRemoveCampaignPhoto(RemoveCampaignPhoto $command)
    {
        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->byId($command->getCampaignId());
        $campaign->setCampaignPhoto(null);

        $this->campaignRepository->save($campaign);
    }

    /**
     * Dispatches the command to the appropriate handler.
     */
    public function handle($command)
    {
        if ($command instanceof CreateCampaign) {
            return $this->handleCreateCampaign($command);
        }
        if ($command instanceof UpdateCampaign) {
            return $this->handleUpdateCampaign($command);
        }
        if ($command instanceof ChangeCampaignState) {
            return $this->handleChangeCampaignState($command);
        }
        if ($command instanceof SetCampaignPhoto) {
            return $this->handleSetCampaignPhoto($command);
        }
        if ($command instanceof RemoveCampaignPhoto) {
            return $this->handleRemoveCampaignPhoto($command);
        }
        throw new \InvalidArgumentException('Unknown command type: ' . get_class($command));
    }
}
