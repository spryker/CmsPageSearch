<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Business\StateMachine;

use Propel\Runtime\Connection\ConnectionInterface;
use Spryker\Zed\StateMachine\Business\Exception\StateMachineException;

class StateUpdater implements StateUpdaterInterface
{

    /**
     * @var \Spryker\Zed\StateMachine\Business\StateMachine\TimeoutInterface
     */
    protected $timeout;

    /**
     * @var \Spryker\Zed\StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected $stateMachineHandlerResolver;

    /**
     * @var \Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface
     */
    protected $stateMachinePersistence;

    /**
     * @var \Propel\Runtime\Connection\ConnectionInterface
     */
    protected $propelConnection;

    /**
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\TimeoutInterface $timeout
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\HandlerResolverInterface $stateMachineHandlerResolver
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface $stateMachinePersistence
     * @param \Propel\Runtime\Connection\ConnectionInterface $propelConnection
     */
    public function __construct(
        TimeoutInterface $timeout,
        HandlerResolverInterface $stateMachineHandlerResolver,
        PersistenceInterface $stateMachinePersistence,
        ConnectionInterface $propelConnection
    ) {
        $this->timeout = $timeout;
        $this->stateMachineHandlerResolver = $stateMachineHandlerResolver;
        $this->stateMachinePersistence = $stateMachinePersistence;
        $this->propelConnection = $propelConnection;
    }

    /**
     * @param string $stateMachineName
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer[] $stateMachineItems
     * @param \Spryker\Zed\StateMachine\Business\Process\ProcessInterface[] $processes
     * @param string[] $sourceStateBuffer
     *
     * @throws \Spryker\Zed\StateMachine\Business\Exception\StateMachineException
     *
     * @return void
     */
    public function updateStateMachineItemState(
        $stateMachineName,
        array $stateMachineItems,
        array $processes,
        array $sourceStateBuffer
    ) {

        if (count($stateMachineItems) === 0) {
            return;
        }

        $this->propelConnection->beginTransaction();

        foreach ($stateMachineItems as $stateMachineItemTransfer) {
            $stateMachineItemTransfer->requireProcessName()
                ->requireIdentifier()
                ->requireStateName();

            $process = $processes[$stateMachineItemTransfer->getProcessName()];

            if (!isset($sourceStateBuffer[$stateMachineItemTransfer->getIdentifier()])) {
                throw new StateMachineException(
                    sprintf('Could not update state, source state not found.')
                );
            }

            $sourceState = $sourceStateBuffer[$stateMachineItemTransfer->getIdentifier()];
            $targetState = $stateMachineItemTransfer->getStateName();

            if ($sourceState !== $targetState) {

                $this->timeout->dropOldTimeout($process, $sourceState, $stateMachineItemTransfer);
                $this->timeout->setNewTimeout($process, $stateMachineItemTransfer);

                $stateMachineHandler = $this->stateMachineHandlerResolver->get($stateMachineName);
                $stateMachineHandler->itemStateUpdated($stateMachineItemTransfer);

                $this->stateMachinePersistence->saveItemStateHistory($stateMachineItemTransfer);

            }
        }

        $this->propelConnection->commit();
    }

}
