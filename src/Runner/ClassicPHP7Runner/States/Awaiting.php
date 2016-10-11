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
use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;

/**
 * State Awaiting
 * @mixin ClassicPHP7Runner
 */
class Awaiting implements StateInterface
{
    use StateTrait;

    private function checkRequirements()
    {
        return function (PHPCode $code) {
            $capabilities = $this->getCapabilities();

            foreach ($code->getNeededPackages() as $package) {
                $packageFound = false;

                /**
                 * @var CapabilityInterface $capability
                 */
                foreach ($capabilities as $capability) {
                    if ('package' == $capability->getType() && $package == $capability) {
                        $packageFound = true;
                        break;
                    }
                }

                if (false === $packageFound) {
                    throw new \RuntimeException("Package $package is not available");
                }
            }
        };
    }

    private function rejectTask()
    {
        /**
         * @param RunnerManagerInterface $manager
         * @param TaskInterface $task
         */
        return function (RunnerManagerInterface $manager, TaskInterface $task) {
            $manager->taskRejected($this, $task);

            $this->updateStates();
        };
    }

    private function acceptTask()
    {
        /**
         * @param RunnerManagerInterface $manager
         * @param TaskInterface $task
         */
        return function (RunnerManagerInterface $manager, TaskInterface $task) {
            $this->currentTask = $task;
            $this->currentResult = null;
            $this->currentManager = $manager;

            $manager->taskAccepted($this, $task);

            $this->currentManager->pushStatus($this, new Status('Registered'));

            $this->updateStates();
        };
    }

    private function doCanYouExecute()
    {
        /**
         * @param RunnerManagerInterface $manager
         * @param TaskInterface $task
         * @return RunnerInterface
         */
        return function (RunnerManagerInterface $manager, TaskInterface $task): RunnerInterface {
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

            $this->run();

            return $this;
        };
    }

    private function doReset()
    {
        /**
         * {@inheritdoc}
         */
        return function (): RunnerInterface {
            return $this;
        };
    }
}