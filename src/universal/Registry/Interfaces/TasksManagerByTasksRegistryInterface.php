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

namespace Teknoo\East\CodeRunner\Registry\Interfaces;

use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\Foundation\Promise\PromiseInterface;

/**
 * Interface TasksManagerByTasksRegistryInterface.
 * Interface to define a registry able to persist the task manager managing a task.
 * Manager are identified by their id referenced in the platform, but manager must be referenced into the registry.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
interface TasksManagerByTasksRegistryInterface
{
    /**
     * To get the manager instance owning the task is passed to promise.
     *
     * @param TaskInterface    $task
     * @param PromiseInterface $promise
     *
     * @return TasksManagerByTasksRegistryInterface
     */
    public function get(TaskInterface $task, PromiseInterface $promise): TasksManagerByTasksRegistryInterface;

    /**
     * To register a manager owning a task.
     *
     * @param TaskInterface        $task
     * @param TaskManagerInterface $manager
     *
     * @return TasksManagerByTasksRegistryInterface
     */
    public function register(TaskInterface $task, TaskManagerInterface $manager): TasksManagerByTasksRegistryInterface;

    /**
     * To forged the manager owning a task.
     *
     * @param TaskInterface $task
     *
     * @return TasksManagerByTasksRegistryInterface
     */
    public function remove(TaskInterface $task): TasksManagerByTasksRegistryInterface;

    /**
     * Register an instance of manager to be able to return it via the method get().
     *
     * @param TaskManagerInterface $taskManager
     *
     * @return TasksManagerByTasksRegistryInterface
     */
    public function addTaskManager(TaskManagerInterface $taskManager): TasksManagerByTasksRegistryInterface;

    /**
     * To clear all runners memorized tasks in the persistent dbms.
     *
     * @return TasksManagerByTasksRegistryInterface
     */
    public function clearAll(): TasksManagerByTasksRegistryInterface;
}
