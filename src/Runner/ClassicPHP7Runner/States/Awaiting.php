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
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east/coderunner Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\East\CodeRunnerBundle\Runner\ClassicPHP7Runner\States;

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\CapabilityInterface;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\ClassicPHP7Runner\ClassicPHP7Runner;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\East\CodeRunnerBundle\Task\PHPCode;
use Teknoo\East\CodeRunnerBundle\Task\Status;
use Teknoo\States\State\AbstractState;

/**
 * State Awaiting
 * @mixin ClassicPHP7Runner
 */
class Awaiting extends AbstractState
{
    private function checkRequirements(PHPCode $code)
    {
        $capabilities = $this->getCapabilities();

        foreach ($code->getNeededPackages() as $package) {
            $packageFound = false;

            /**
             * @var CapabilityInterface $capability
             */
            foreach ($capabilities as $capability) {
                if ('package' == $capability->getType() && $package == $capability->getValue()) {
                    $packageFound = true;
                    break;
                }
            }

            if (false === $packageFound) {
                throw new \RuntimeException("Package $package is not available");
            }
        }
    }

    /**
     * @param RunnerManagerInterface $manager
     * @param TaskInterface $task
     */
    private function rejectTask(RunnerManagerInterface $manager, TaskInterface $task)
    {
        $manager->taskRejected($this, $task);

        $this->updateStates();
    }

    /**
     * @param RunnerManagerInterface $manager
     * @param TaskInterface $task
     */
    private function acceptTask(RunnerManagerInterface $manager, TaskInterface $task)
    {
        $this->currentTask = $task;
        $this->currentResult = null;
        $this->currentManager = $manager;

        $manager->taskAccepted($this, $task);

        $this->currentManager->pushStatus($this, new Status('Registered'));

        $this->updateStates();
    }

    /**
     * @param RunnerManagerInterface $manager
     * @param TaskInterface $task
     * @return RunnerInterface
     */
    private function doCanYouExecute(RunnerManagerInterface $manager, TaskInterface $task): RunnerInterface
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

        $this->updateStates();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    private function doReset(): RunnerInterface
    {
        return $this;
    }
}