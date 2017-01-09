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

namespace Teknoo\East\CodeRunner\Manager\Interfaces;

use Teknoo\East\CodeRunner\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;

/**
 * A runner manager is a service able to register all available runner for a platform and dispatch execution on
 * these runner according to theirs capabilities.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
interface RunnerManagerInterface
{
    /**
     * To register a runner in the manager to be able to send it a task to execute.
     *
     * @param RunnerInterface $runner
     *
     * @return RunnerManagerInterface
     */
    public function registerMe(RunnerInterface $runner): RunnerManagerInterface;

    /**
     * To forget a runner from this manager, all tasks in execution are lost.
     *
     *
     * @param RunnerInterface $runner
     *
     * @return RunnerManagerInterface
     */
    public function forgetMe(RunnerInterface $runner): RunnerManagerInterface;

    /**
     * To retrieve a result from an execution, pushed by a runner.
     *
     * @param RunnerInterface $runner
     * @param ResultInterface $result
     *
     * @return RunnerManagerInterface
     *
     * @throws \DomainException if the result is not valid for a task registered in the manager
     */
    public function pushResult(RunnerInterface $runner, ResultInterface $result): RunnerManagerInterface;

    /**
     * To allow a runner to update a status of a task.
     *
     * @param RunnerInterface $runner
     * @param StatusInterface $status
     *
     * @return RunnerManagerInterface
     */
    public function pushStatus(RunnerInterface $runner, StatusInterface $status): RunnerManagerInterface;

    /**
     * Called by a runner to inform the manager that it accept to execute the task.
     *
     * @param RunnerInterface $runner
     * @param TaskInterface   $task
     *
     * @return RunnerManagerInterface
     *
     * @throws \DomainException if the result is not valid for a task registered in the manager
     */
    public function taskAccepted(RunnerInterface $runner, TaskInterface $task): RunnerManagerInterface;

    /**
     * Called by a runner to inform the manager that it does not accept to execute the task.
     *
     * @param RunnerInterface $runner
     * @param TaskInterface   $task
     *
     * @return RunnerManagerInterface
     *
     * @throws \DomainException if the result is not valid for a task registered in the manager
     */
    public function taskRejected(RunnerInterface $runner, TaskInterface $task): RunnerManagerInterface;

    /**
     * To execute a Task, sent by a task manager on a dedicated runner.
     *
     * @param TaskManagerInterface $taskManager
     * @param TaskInterface        $task
     *
     * @return RunnerManagerInterface
     *
     * @throws \DomainException if the task is not executable by the runner
     */
    public function executeForMeThisTask(TaskManagerInterface $taskManager, TaskInterface $task): RunnerManagerInterface;
}
