<?php

/**
 * East CodeRunnerBundle.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east/coderunner Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\East\CodeRunner\Runner;

use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Runner\Interfaces\CapabilityInterface;
use Teknoo\East\CodeRunner\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\CodeRunner\Task\PHPCode;

/**
 * Trait to provide to runners the business logic to accept or reject a task after checks its requirements.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @mixin RunnerInterface
 */
trait CheckRequirementsTrait
{
    /**
     * Method to check if all requirements needed by the task is available on the runner.
     *
     * @param PHPCode $code
     *
     * @throws \RuntimeException
     */
    private function checkRequirements(PHPCode $code)
    {
        $capabilities = $this->getCapabilities();

        foreach ($code->getNeededCapabilities() as $neededCapability) {
            $capabilityFound = false;

            /**
             * @var CapabilityInterface
             */
            foreach ($capabilities as $capability) {
                if ($neededCapability == $capability) {
                    $capabilityFound = true;
                    break;
                }
            }

            if (false === $capabilityFound) {
                throw new \RuntimeException("Capability $neededCapability is not available");
            }
        }
    }

    /**
     * To indicate to the manager that the task has been rejected by the runner.
     *
     * @param RunnerManagerInterface $manager
     * @param TaskInterface          $task
     */
    private function rejectTask(RunnerManagerInterface $manager, TaskInterface $task)
    {
        $manager->taskRejected($this, $task);

        $this->updateStates();
    }

    /**
     * To indicate to the manager that the task has been accepted by the runner.
     *
     * @param RunnerManagerInterface $manager
     * @param TaskInterface          $task
     */
    private function acceptTask(RunnerManagerInterface $manager, TaskInterface $task)
    {
        $manager->taskAccepted($this, $task);

        $this->updateStates();
    }

    /**
     * {@inheritdoc}
     */
    public function canYouExecute(RunnerManagerInterface $manager, TaskInterface $task): RunnerInterface
    {
        $code = $task->getCode();

        if (!$code instanceof PHPCode) {
            $this->rejectTask($manager, $task);

            return $this;
        }

        try {
            $this->checkRequirements($code);
        } catch (\Throwable $t) {
            $this->rejectTask($manager, $task);

            return $this;
        }

        $this->acceptTask($manager, $task);

        return $this;
    }
}
