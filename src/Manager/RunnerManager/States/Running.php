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
namespace Teknoo\East\CodeRunnerBundle\Manager\RunnerManager\States;

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunnerBundle\Manager\RunnerManager\RunnerManager;
use Teknoo\East\CodeRunnerBundle\Registry\Interfaces\TasksByRunnerRegistryInterface;
use Teknoo\East\CodeRunnerBundle\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\States\State\AbstractState;

/**
 * State Running
 * @property RunnerInterface[] $runners
 * @property TasksByRunnerRegistryInterface|TaskInterface[] $tasksByRunner
 * @property TasksManagerByTasksRegistryInterface|TaskManagerInterface[] $tasksManagerByTasks
 * @mixin RunnerManager
 */
class Running extends AbstractState
{
    /**
     * {@inheritdoc}
     */
    private function doRegisterMe(RunnerInterface $runner): RunnerManagerInterface
    {
        $this->runners[$runner->getIdentifier()] = $runner;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    private function doForgetMe(RunnerInterface $runner): RunnerManagerInterface
    {
        $runnerIdentifier = $runner->getIdentifier();
        if (isset($this->runners[$runnerIdentifier])) {
            unset($this->runners[$runnerIdentifier]);
        }

        return $this;
    }

    /**
     * Method to clear a runner after its execution and free memory in this runner about this task.
     *
     * @param RunnerInterface $runner
     * @param TaskInterface $task
     */
    private function clearRunner(RunnerInterface $runner, TaskInterface $task)
    {
        $runner->reset();
        unset($this->tasksByRunner[$runner->getIdentifier()]);
        unset($this->tasksManagerByTasks[$task->getUrl()]);
    }

    /**
     * {@inheritdoc}
     */
    private function doPushResult(RunnerInterface $runner, ResultInterface $result): RunnerManagerInterface
    {
        $runnerIdentifier = $runner->getIdentifier();
        if (!isset($this->tasksByRunner[$runnerIdentifier])) {
            throw new \DomainException('Error, the task was not found for this runner');
        }

        $task = $this->tasksByRunner[$runnerIdentifier];
        if (!isset($this->tasksManagerByTasks[$task->getUrl()])) {
            throw new \DomainException('Error, the task was not found for this runner');
        }

        $taskManager = $this->tasksManagerByTasks[$task->getUrl()];
        $taskManager->taskResultIsUpdated($task, $result);

        $this->clearRunner($runner, $task);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    private function doPushStatus(RunnerInterface $runner, StatusInterface $status): RunnerManagerInterface
    {
        $runnerIdentifier = $runner->getIdentifier();
        if (!isset($this->tasksByRunner[$runnerIdentifier])) {
            throw new \DomainException('Error, the task was not found for this runner');
        }

        $task = $this->tasksByRunner[$runnerIdentifier];
        if (!isset($this->tasksManagerByTasks[$task->getUrl()])) {
            throw new \DomainException('Error, the task was not found for this runner');
        }

        $taskManager = $this->tasksManagerByTasks[$task->getUrl()];
        $taskManager->taskStatusIsUpdated($task, $status);

        return $this;
    }

    /**
     * To register in the local area the task to be able find it in next operations
     * @param RunnerInterface $runner
     * @param TaskInterface $task
     * @param TaskManagerInterface $taskManager
     * @return RunnerManager
     */
    private function registerTask(RunnerInterface $runner, TaskInterface $task, TaskManagerInterface $taskManager): RunnerManager
    {
        $this->tasksByRunner[$runner->getIdentifier()] = $task;
        $this->tasksManagerByTasks[$task->getUrl()] = $taskManager;

        return $this;
    }
}