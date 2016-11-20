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

namespace Teknoo\East\CodeRunner\Manager\RunnerManager;

use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Manager\RunnerManager\States\Running;
use Teknoo\East\CodeRunner\Manager\RunnerManager\States\Selecting;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksByRunnerRegistryInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksStandbyRegistryInterface;
use Teknoo\East\CodeRunner\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\Proxy\ProxyTrait;

/**
 * @method RunnerManager doRegisterMe(RunnerInterface $runner)
 * @method RunnerManager doForgetMe(RunnerInterface $runner)
 * @method RunnerManager doPushResult(RunnerInterface $runner, ResultInterface $result)
 * @method RunnerManager doPushStatus(RunnerInterface $runner, StatusInterface $status)
 * @method RunnerManager doTaskAccepted(RunnerInterface $runner, TaskInterface $task)
 * @method RunnerManager doTaskRejected(RunnerInterface $runner, TaskInterface $task)
 * @method RunnerInterface selectRunnerToExecuteTask(TaskInterface  $task)
 * @method RunnerManager registerTask(RunnerInterface $runner, TaskInterface  $task, TaskManagerInterface $manager)
 */
class RunnerManager implements ProxyInterface, RunnerManagerInterface
{
    use ProxyTrait;

    /**
     * @var RunnerInterface[]
     */
    private $runners = [];

    /**
     * @var TasksByRunnerRegistryInterface|TaskInterface[]
     */
    private $tasksByRunner;

    /**
     * @var TasksManagerByTasksRegistryInterface|TaskManagerInterface[]
     */
    private $tasksManagerByTasks;

    /**
     * @var TasksStandbyRegistryInterface
     */
    private $tasksStandbyRegistry;

    /**
     * @var bool
     */
    private $taskAcceptedByARunner = false;

    /**
     * @var RunnerInterface
     */
    private $runnerAccepted = null;

    /**
     * Manager constructor.
     * Initialize States behavior.
     *
     * @param TasksByRunnerRegistryInterface       $tasksByRunner
     * @param TasksManagerByTasksRegistryInterface $tasksManagerByTasks
     * @param TasksStandbyRegistryInterface        $tasksStandbyRegistry
     */
    public function __construct(
        TasksByRunnerRegistryInterface $tasksByRunner,
        TasksManagerByTasksRegistryInterface $tasksManagerByTasks,
        TasksStandbyRegistryInterface $tasksStandbyRegistry
    ) {
        $this->tasksByRunner = $tasksByRunner;
        $this->tasksManagerByTasks = $tasksManagerByTasks;
        $this->tasksStandbyRegistry = $tasksStandbyRegistry;

        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeProxy();
        //Initialize state
        $this->enableState(Running::class);
    }

    /**
     * {@inheritdoc}
     */
    public static function statesListDeclaration(): array
    {
        return [
            Running::class,
            Selecting::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerMe(RunnerInterface $runner): RunnerManagerInterface
    {
        return $this->doRegisterMe($runner);
    }

    /**
     * {@inheritdoc}
     */
    public function forgetMe(RunnerInterface $runner): RunnerManagerInterface
    {
        return $this->doForgetMe($runner);
    }

    /**
     * {@inheritdoc}
     */
    public function pushResult(RunnerInterface $runner, ResultInterface $result): RunnerManagerInterface
    {
        return $this->doPushResult($runner, $result);
    }

    /**
     * {@inheritdoc}
     */
    public function pushStatus(RunnerInterface $runner, StatusInterface $status): RunnerManagerInterface
    {
        return $this->doPushStatus($runner, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function taskAccepted(RunnerInterface $runner, TaskInterface $task): RunnerManagerInterface
    {
        return $this->doTaskAccepted($runner, $task);
    }

    /**
     * {@inheritdoc}
     */
    public function taskRejected(RunnerInterface $runner, TaskInterface $task): RunnerManagerInterface
    {
        return $this->doTaskRejected($runner, $task);
    }

    /**
     * The runner can select a new runner only in selecting states, else useful methods to perform this operation are
     * not available.
     *
     * @return RunnerManager
     */
    public function switchToSelectingTask(): RunnerManager
    {
        $this->switchState(Selecting::class);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function executeForMeThisTask(TaskManagerInterface $taskManager, TaskInterface $task): RunnerManagerInterface
    {
        //Find and select the good runner to execute the task
        $runnerManager = clone $this;
        $runnerManager->switchToSelectingTask();
        $runner = $runnerManager->selectRunnerToExecuteTask($task);

        //No exception, so register the task with the good runner
        return $this->registerTask($runner, $task, $taskManager);
    }
}
