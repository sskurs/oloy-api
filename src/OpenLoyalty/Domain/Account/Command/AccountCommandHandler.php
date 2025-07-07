<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Account\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\EventDispatcher\EventDispatcherInterface;
use OpenLoyalty\Domain\Account\Account;
use OpenLoyalty\Domain\Account\AccountRepository;
use OpenLoyalty\Domain\Account\SystemEvent\AccountCreatedSystemEvent;
use OpenLoyalty\Domain\Account\SystemEvent\AccountSystemEvents;
use OpenLoyalty\Domain\Account\SystemEvent\AvailablePointsAmountChangedSystemEvent;

/**
 * Class AccountCommandHandler.
 */
class AccountCommandHandler implements CommandHandler
{
    /**
     * @var AccountRepository
     */
    protected $repository;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * AccountCommandHandler constructor.
     *
     * @param AccountRepository        $repository
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(AccountRepository $repository, EventDispatcherInterface $eventDispatcher = null)
    {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handleCreateAccount(CreateAccount $command)
    {
        /** @var Account $account */
        $account = Account::createAccount($command->getAccountId(), $command->getCustomerId());
        $this->repository->save($account);
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(
                AccountSystemEvents::ACCOUNT_CREATED,
                [new AccountCreatedSystemEvent($account->getId(), $command->getCustomerId())]
            );
        }
    }

    public function handleAddPoints(AddPoints $command)
    {
        /** @var Account $account */
        $account = $this->repository->load($command->getAccountId());
        $account->addPoints($command->getPointsTransfer());
        $this->repository->save($account);
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(
                AccountSystemEvents::AVAILABLE_POINTS_AMOUNT_CHANGED,
                [new AvailablePointsAmountChangedSystemEvent(
                    $account->getId(),
                     $account->getCustomerId(),
                     $account->getAvailableAmount(),
                     $command->getPointsTransfer()->getValue(),
                     AvailablePointsAmountChangedSystemEvent::OPERATION_TYPE_ADD
                 )]
            );
        }
    }

    public function handleSpendPoints(SpendPoints $command)
    {
        /** @var Account $account */
        $account = $this->repository->load($command->getAccountId());
        $account->spendPoints($command->getPointsTransfer());
        $this->repository->save($account);
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(
                AccountSystemEvents::AVAILABLE_POINTS_AMOUNT_CHANGED,
                [
                    new AvailablePointsAmountChangedSystemEvent(
                        $account->getId(),
                        $account->getCustomerId(),
                        $account->getAvailableAmount(),
                        $command->getPointsTransfer()->getValue()
                    ), ]
            );
        }
    }

    public function handleCancelPointsTransfer(CancelPointsTransfer $command)
    {
        /** @var Account $account */
        $account = $this->repository->load($command->getAccountId());
        $account->cancelPointsTransfer($command->getPointsTransferId());
        $this->repository->save($account);
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(
                AccountSystemEvents::AVAILABLE_POINTS_AMOUNT_CHANGED,
                [
                    new AvailablePointsAmountChangedSystemEvent(
                        $account->getId(),
                        $account->getCustomerId(),
                        $account->getAvailableAmount()
                    ),
                ]
            );
        }
    }

    public function handleExpirePointsTransfer(ExpirePointsTransfer $command)
    {
        /** @var Account $account */
        $account = $this->repository->load($command->getAccountId());
        $account->expirePointsTransfer($command->getPointsTransferId());
        $this->repository->save($account);
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(
                AccountSystemEvents::AVAILABLE_POINTS_AMOUNT_CHANGED,
                [
                    new AvailablePointsAmountChangedSystemEvent(
                        $account->getId(),
                        $account->getCustomerId(),
                        $account->getAvailableAmount()),
                ]
            );
        }
    }

    /**
     * Dispatches the command to the appropriate handler.
     */
    public function handle($command)
    {
        if ($command instanceof CreateAccount) {
            return $this->handleCreateAccount($command);
        }
        if ($command instanceof AddPoints) {
            return $this->handleAddPoints($command);
        }
        if ($command instanceof SpendPoints) {
            return $this->handleSpendPoints($command);
        }
        if ($command instanceof CancelPointsTransfer) {
            return $this->handleCancelPointsTransfer($command);
        }
        if ($command instanceof ExpirePointsTransfer) {
            return $this->handleExpirePointsTransfer($command);
        }
        throw new \InvalidArgumentException('Unknown command type: ' . get_class($command));
    }
}
