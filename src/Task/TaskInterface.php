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
namespace Teknoo\East\CodeRunnerBundle\Task;

use Teknoo\East\CodeRunnerBundle\Manager\TaskManagerInterface;

/**
 * Interface to define task class, able to trace the process under all service.
 *
 *
 * @package Teknoo\East\CodeRunnerBundle\Task
 */
interface TaskInterface
{
    /**
     * To register the code, as value object, to execute
     * @param CodeInterface $code
     * @return TaskInterface
     */
    public function setCode(CodeInterface $code): TaskInterface;

    /**
     * Getter to get the code to execute in a runner
     *
     * @return CodeInterface
     */
    public function getCode(): CodeInterface;

    /**
     * Url to identify the task to execute
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Status of the task, as value object
     *
     * @return StatusInterface
     */
    public function getStatus(): StatusInterface;

    /**
     * Result of the task, as value object
     *
     * @return ResultInterface
     */
    public function getResult(): ResultInterface;

    /**
     * To save the task manager whom execute this task
     * @param string $taskUrl
     * @param TaskManagerInterface $taskManager
     * @return TaskInterface
     */
    public function registerTaskManagerExecuting(string $taskUrl, TaskManagerInterface $taskManager): TaskInterface;

    /**
     * To register a result of this task from a task manager
     * @param TaskManagerInterface $taskManager
     * @param ResultInterface $result
     * @return TaskInterface
     */
    public function registerResult(TaskManagerInterface $taskManager, ResultInterface $result): TaskInterface;
}