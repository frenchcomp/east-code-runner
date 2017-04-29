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

namespace Teknoo\East\CodeRunner\Manager\RunnerManager\States;

use Psr\Log\LoggerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Manager\RunnerManager\RunnerManager;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksByRunnerRegistryInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunner\Registry\TasksStandbyRegistry;
use Teknoo\East\CodeRunner\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\Foundation\Promise\Promise;
use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;

/**
 * State Running.
 * RunnerManager's state to manage task registration from task manager, and return from runner to update tasks via tasks
 * manager.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @property RunnerInterface[] $runners
 * @property TasksByRunnerRegistryInterface $tasksByRunner
 * @property TasksManagerByTasksRegistryInterface $tasksManagerByTasks
 * @property TasksStandbyRegistry $tasksStandbyRegistry
 * @property LoggerInterface $logger
 * @mixin RunnerManager
 */
class Running implements StateInterface
{
    use StateTrait;

    private function doRegisterMe()
    {
        /*
         * {@inheritdoc}
         */
        return function (RunnerInterface $runner): RunnerManagerInterface {
            $runners = $this->runners;
            $runners[$runner->getIdentifier()] = $runner;
            $this->runners = $runners;

            $this->tasksByRunner->get(
                $runner,
                new Promise(function (TaskInterface $task) use ($runner) {
                    $runner->rememberYourCurrentTask($task);
                })
            );

            return $this;
        };
    }

    private function doForgetMe()
    {
        /*
         * {@inheritdoc}
         */
        return function (RunnerInterface $runner): RunnerManagerInterface {
            $runnerIdentifier = $runner->getIdentifier();
            $runners = $this->runners;
            if (isset($this->runners[$runnerIdentifier])) {
                unset($runners[$runnerIdentifier]);
                $this->runners = $runners;
            }

            return $this;
        };
    }

    private function clearRunner()
    {
        /*
         * Method to clear a runner after its execution and free memory in this runner about this task.
         *
         * @param RunnerInterface $runner
         * @param TaskInterface $task
         */
        return function (RunnerInterface $runner, TaskInterface $task) {
            $runner->prepareNextTask();
            $this->tasksByRunner->remove($runner);
            $this->loadNextTaskFor($runner);
        };
    }

    private function doPushResult()
    {
        /*
         * {@inheritdoc}
         */
        return function (
            RunnerInterface $runner,
            TaskInterface $task,
            ResultInterface $result
        ): RunnerManagerInterface {
            $this->tasksManagerByTasks->get(
                $task,
                new Promise(
                    function (TaskManagerInterface $taskManager) use ($task, $result) {
                        $taskManager->taskResultIsUpdated($task, $result);
                    },
                    function ($exception) {
                        throw $exception;
                    }
                )
            );

            return $this;
        };
    }

    private function doPushStatus()
    {
        /*
         * {@inheritdoc}
         */
        return function (
            RunnerInterface $runner,
            TaskInterface $task,
            StatusInterface $status
        ): RunnerManagerInterface {
            $this->tasksManagerByTasks->get(
                $task,
                new Promise(
                    function (TaskManagerInterface $taskManager) use ($runner, $task, $status) {
                        $taskManager->taskStatusIsUpdated($task, $status);

                        if ($runner->supportsMultiplesTasks()) {
                            $this->tasksByRunner->get(
                                $runner,
                                new Promise(function (TaskInterface $currentTaskExecuted) use ($runner, $task) {
                                    if ($task->getUrl() == $currentTaskExecuted->getUrl()) {
                                        //It's the task currently initializing by the runner,
                                        // inform it to switch to next task
                                        $this->clearRunner($runner, $task);
                                    }
                                })
                            );
                        } elseif (!$runner->supportsMultiplesTasks() && $status->isFinal()) {
                            $this->clearRunner($runner, $task);
                        }
                    },
                    function (\Throwable $exception) {
                        throw $exception;
                    }
                )
            );

            return $this;
        };
    }

    private function registerTask()
    {
        /*
         * To register in the local area the task to be able find it in next operations
         * @param RunnerInterface $runner
         * @param TaskInterface $task
         * @param TaskManagerInterface $taskManager
         * @return RunnerManager
         */
        return function (
            RunnerInterface $runner,
            TaskInterface $task,
            TaskManagerInterface $taskManager
        ): RunnerManager {
            $this->tasksStandbyRegistry->enqueue($runner, $task);
            $this->tasksManagerByTasks->get(
                $task,
                new Promise(null, function () use ($task, $taskManager) {
                    //To prevent some issue if manager had not already registerd itself
                    $this->tasksManagerByTasks->register($task, $taskManager);
                })
            );

            $this->loadNextTaskFor($runner);

            return $this;
        };
    }

    public function loadNextTaskFor()
    {
        /*
         * To ask a specific runner if it idles to start another task in its list.
         * @param RunnerInterface $runner
         * @return RunnerManager
         */
        return function (RunnerInterface $runner): RunnerManager {
            $this->tasksByRunner->get(
                $runner,
                new Promise(null, function () use ($runner) {
                    $this->tasksStandbyRegistry->dequeue(
                        $runner,
                        new Promise(function ($taskStandBy) use ($runner) {
                            try {
                                $runner->execute($this, $taskStandBy);
                                $this->tasksByRunner->register($runner, $taskStandBy);
                            } catch (\Throwable $e) {
                                if ($this->logger instanceof LoggerInterface) {
                                    $this->logger->critical($e->getMessage().PHP_EOL.$e->getTraceAsString());
                                }

                                $this->tasksStandbyRegistry->enqueue($runner, $taskStandBy);
                                throw $e;
                            }
                        })
                    );
                })
            );

            return $this;
        };
    }

    public function loadNextTasks()
    {
        /*
         * To browse all runner to check if they idle and ask them to start another task.
         * @return RunnerManager
         */
        return function (): RunnerManager {
            foreach ($this->runners as $runner) {
                $this->loadNextTaskFor($runner);
            }

            return $this;
        };
    }
}
