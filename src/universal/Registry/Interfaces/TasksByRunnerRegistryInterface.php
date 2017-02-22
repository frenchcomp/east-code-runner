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

use Teknoo\East\CodeRunner\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\Foundation\Promise\PromiseInterface;

/**
 * Interface TasksByRunnerRegistryInterface.
 * Interface to define a registry able to persist the task currently executed by a runner.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
interface TasksByRunnerRegistryInterface
{
    /**
     * To get the task currently executed by a runner, the task is passed to promise.
     *
     * @param RunnerInterface  $runner
     * @param PromiseInterface $promise
     *
     * @return TasksByRunnerRegistryInterface
     */
    public function get(RunnerInterface $runner, PromiseInterface $promise): TasksByRunnerRegistryInterface;

    /**
     * To register a task currently executed by a runner.
     *
     * @param RunnerInterface $runner
     * @param TaskInterface   $task
     *
     * @return TasksByRunnerRegistryInterface
     */
    public function register(RunnerInterface $runner, TaskInterface $task): TasksByRunnerRegistryInterface;

    /**
     * To remove a reference about task currently executed by a runner.
     *
     * @param RunnerInterface $runner
     *
     * @return TasksByRunnerRegistryInterface
     */
    public function remove(RunnerInterface $runner): TasksByRunnerRegistryInterface;

    /**
     * To clear all runners memorized tasks in the persistent dbms.
     *
     * @return TasksByRunnerRegistryInterface
     */
    public function clearAll(): TasksByRunnerRegistryInterface;
}
