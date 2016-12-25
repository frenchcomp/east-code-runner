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

namespace Teknoo\East\CodeRunner\Runner\Interfaces;

use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;

/**
 * To define a runner able to execute tasks.
 */
interface RunnerInterface
{
    /**
     * To know the unique identifier about a runner.
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * To identify the runner.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * To know the version used for the VM (or libc) to execute tasks.
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * To know capabilities provided by runner, as array of CapabilityInterface objects.
     *
     * @return array
     */
    public function getCapabilities(): array;

    /**
     * To inform a system that can return to its initial state and forgot
     * the last execution in progress and advance to the next.
     *
     * @return RunnerInterface
     */
    public function reset(): RunnerInterface;

    /**
     * To restore in the runner its current task in execution (for asynchronous runners).
     *
     * @param TaskInterface $task
     *
     * @return RunnerInterface
     */
    public function rememberYourCurrentTask(TaskInterface $task): RunnerInterface;

    /**
     * To check if a task is executable by the runner, the runner must recall to the manager the method accept or reject.
     *
     * @param RunnerManagerInterface $manager
     * @param TaskInterface          $task
     *
     * @return RunnerInterface
     *
     * @throws \DomainException if the task is not executable by the runner
     * @throws \LogicException  if the task's code is invalid
     */
    public function canYouExecute(RunnerManagerInterface $manager, TaskInterface $task): RunnerInterface;

    /**
     * To execute the task by the runner, the runner must call methods pushResult and pushStatus to notify the manager.
     *
     * @param RunnerManagerInterface $manager
     * @param TaskInterface          $task
     *
     * @return RunnerInterface
     *
     * @throws \LogicException if the task's code is invalid
     */
    public function execute(RunnerManagerInterface $manager, TaskInterface $task): RunnerInterface;
}
