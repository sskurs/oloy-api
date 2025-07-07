<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Pos\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\EventDispatcher\EventDispatcher;
use OpenLoyalty\Domain\Pos\Pos;
use OpenLoyalty\Domain\Pos\PosRepository;
use OpenLoyalty\Domain\Pos\SystemEvent\PosSystemEvents;
use OpenLoyalty\Domain\Pos\SystemEvent\PosUpdatedSystemEvent;

/**
 * Class PosCommandHandler.
 */
class PosCommandHandler implements CommandHandler
{
    /**
     * @var PosRepository
     */
    protected $posRepository;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * PosCommandHandler constructor.
     *
     * @param PosRepository   $posRepository
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(PosRepository $posRepository, EventDispatcher $eventDispatcher = null)
    {
        $this->posRepository = $posRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handleCreatePos(CreatePos $command)
    {
        $pos = new Pos($command->getPosId(), $command->getPosData());
        $this->posRepository->save($pos);
    }

    public function handleUpdatePos(UpdatePos $command)
    {
        /** @var Pos $pos */
        $pos = $this->posRepository->byId($command->getPosId());
        $pos->setFromArray($command->getPosData());
        $this->posRepository->save($pos);
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(PosSystemEvents::POS_UPDATED, [
                new PosUpdatedSystemEvent($command->getPosId(), $pos->getName(), $pos->getLocation() ? $pos->getLocation()->getCity() : null),
            ]);
        }
    }

    /**
     * Dispatches the command to the appropriate handler.
     */
    public function handle($command)
    {
        if ($command instanceof CreatePos) {
            return $this->handleCreatePos($command);
        }
        if ($command instanceof UpdatePos) {
            return $this->handleUpdatePos($command);
        }
        if ($command instanceof ActivatePos) {
            return $this->handleActivatePos($command);
        }
        if ($command instanceof DeactivatePos) {
            return $this->handleDeactivatePos($command);
        }
        throw new \InvalidArgumentException('Unknown command type: ' . get_class($command));
    }
}
