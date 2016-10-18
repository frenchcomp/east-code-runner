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
namespace Teknoo\East\CodeRunnerBundle\Registry\Interfaces;

use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;

interface TasksStandbyRegistryInterface
{
    /**
     * To add a task in standby list of a runner
     * @param RunnerInterface $runner
     * @param TaskInterface $task
     * @return TasksStandbyRegistryInterface|self
     */
    public function enqueue (RunnerInterface $runner, TaskInterface $task): TasksStandbyRegistryInterface;

    /**
     * Dequeues a standby task for a runner. If there are no standby queue, the method must return null.
     * @param RunnerInterface $runner
     * @return TaskInterface|null
     */
    public function dequeue(RunnerInterface $runner);
    
    /**
     * To clear all standby tasks in the persistent dbms
     *
     * @return TasksStandbyRegistryInterface|self
     */
    public function clearAll(): TasksStandbyRegistryInterface;
}