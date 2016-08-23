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
use Teknoo\East\CodeRunnerBundle\Manager\RunnerManager\States\Selecting;
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
    private function switchToSelectingTask(): RunnerManager
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