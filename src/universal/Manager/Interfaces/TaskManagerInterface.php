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

use Teknoo\East\CodeRunner\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\Foundation\Promise\PromiseInterface;

/**
 * Interface TaskManagerInterface.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
interface TaskManagerInterface
{
    /**
     * To know the unique identifier about a runner.
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * To register the registry in manager.
     *
     * @param TasksManagerByTasksRegistryInterface $registry
     *
     * @return TaskManagerInterface
     */
    public function addRegistry(TasksManagerByTasksRegistryInterface $registry): TaskManagerInterface;

    /**
     * To register the runner manager to contact to run a task for this manager.
     *
     * @param RunnerManagerInterface $runnerManager
     *
     * @return TaskManagerInterface
     */
    public function registerRunnerManager(RunnerManagerInterface $runnerManager): TaskManagerInterface;

    /**
     * To persist a task to execute and sent it to a register.
     *
     * @param TaskInterface    $task
     * @param PromiseInterface $promise
     *
     * @return TaskManagerInterface
     */
    public function executeMe(TaskInterface $task, PromiseInterface $promise): TaskManagerInterface;

    /**
     * Called by the runner manager to inform the task manager an update about a task.
     * Silent fail if the task is not managed by the instance.
     *
     * @param TaskInterface   $task
     * @param StatusInterface $status
     *
     * @return TaskManagerInterface
     */
    public function taskStatusIsUpdated(TaskInterface $task, StatusInterface $status): TaskManagerInterface;

    /**
     * Called by the runner manager to inform the task manager an task's result.
     * Silent fail if the task is not managed by the instance.
     *
     * @param TaskInterface   $task
     * @param ResultInterface $result
     *
     * @return TaskManagerInterface
     */
    public function taskResultIsUpdated(TaskInterface $task, ResultInterface $result): TaskManagerInterface;

    /**
     * To close a task to execute.
     *
     * @param TaskInterface $task
     *
     * @return TaskManagerInterface
     */
    public function forgetMe(TaskInterface $task): TaskManagerInterface;
}
