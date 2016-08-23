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
namespace Teknoo\East\CodeRunnerBundle\Manager\RunnerManager;

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\States\Proxy\Integrated;

class RunnerManager extends Integrated implements RunnerManagerInterface
{
    /**
     * @var RunnerInterface[]
     */
    private $runners = [];

    /**
     * @var array|TaskInterface[]
     */
    private $tasksByRunner = [];

    /**
     * @var array|TaskManagerInterface[]
     */
    private $tasksManagerByTasks = [];

    /**
     * @var bool
     */
    private $taskAcceptedByARunner = false;

    /**
     * @var RunnerInterface
     */
    private $runnerAccepted = null;

    /**
     * {@inheritdoc}
     */
    public function registerMe(RunnerInterface $runner): RunnerManagerInterface
    {
        $this->runners[$runner->getIdentifier()] = $runner;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function forgetMe(RunnerInterface $runner): RunnerManagerInterface
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
    public function pushResult(RunnerInterface $runner, ResultInterface $result): RunnerManagerInterface
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
    public function taskAccepted(RunnerInterface $runner, TaskInterface $task): RunnerManagerInterface
    {
        $this->taskAcceptedByARunner = true;
        $this->runnerAccepted = $runner;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function taskRejected(RunnerInterface $runner, TaskInterface $task): RunnerManagerInterface
    {
        $this->taskAcceptedByARunner = false;

        return $this;
    }

    /**
     * Method to browse all available runner, until any runner has accepted
     * @return \Generator
     */
    private function browseRunners()
    {
        foreach ($this->runners as $runner) {
            yield $runner;

            if (true === $this->taskAcceptedByARunner) {
                break;
            }
        }
    }

    /**
     * To find and select the runner able to execute a task. If no runner found, the method throws the exception
     * \DomainException
     * @param TaskInterface $task
     * @return RunnerInterface
     * @throws \DomainException
     */
    private function selectRunnerToExecuteTask(TaskInterface $task): RunnerInterface
    {
        $this->taskAcceptedByARunner = false;

        foreach ($this->browseRunners() as $runner) {
            /**
             * @var RunnerInterface $runner
             */
            $runner->canYouExecute($this, $task);
        }

        if (false === $this->taskAcceptedByARunner) {
            throw new \DomainException('No runner available to execute the task');
        }

        return $this->runnerAccepted;
    }

    /**
     * {@inheritdoc}
     */
    public function executeForMeThisTask(TaskManagerInterface $taskManager, TaskInterface $task): RunnerManagerInterface
    {
        //Find and select the good runner to execute the task
        $runnerManager = clone $this;
        $runner = $runnerManager->selectRunnerToExecuteTask($task);

        //No exception, so register the task with the good runner
        $this->tasksByRunner[$runner->getIdentifier()] = $task;
        $this->tasksManagerByTasks[$task->getUrl()] = $taskManager;

        return $this;
    }
}