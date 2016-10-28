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
namespace Teknoo\East\CodeRunnerBundle\Task\Interfaces;

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\TaskManagerInterface;

/**
 * Interface to define task class, able to trace the process under all service.
 */
interface TaskInterface extends \JsonSerializable
{
    /**
     * To return the uniq identifier as UUID of the task
     */
    public function getId(): string;

    /**
     * To register the code, as value object, to execute.
     *
     * @param CodeInterface $code
     * @return TaskInterface
     */
    public function setCode(CodeInterface $code): TaskInterface;

    /**
     * Getter to get the code to execute in a runner.
     *
     * @return CodeInterface
     * @throws \UnexpectedValueException if the code missing
     */
    public function getCode(): CodeInterface;

    /**
     * Url to identify the task to execute.
     *
     * @return string
     * @throws \UnexpectedValueException if the url missing
     */
    public function getUrl(): string;

    /**
     * Status of the task, as value object.
     *
     * @return StatusInterface
     */
    public function getStatus(): StatusInterface;

    /**
     * Result of the task, as value object.
     *
     * @return ResultInterface
     * @throws \UnexpectedValueException if the result missing
     */
    public function getResult(): ResultInterface;

    /**
     * To save the url allowed to this task by the task manager.
     *
     * @param string $taskUrl
     * @return TaskInterface
     */
    public function registerUrl(string $taskUrl): TaskInterface;

    /**
     * To save/register the status of the task.
     *
     * @param StatusInterface $status
     * @return TaskInterface
     */
    public function registerStatus(StatusInterface $status): TaskInterface;

    /**
     * To register a result of this task from a task manager. It must update it's result value,
     * available via the method getResult.
     *
     * @param TaskManagerInterface $taskManager
     * @param ResultInterface $result
     * @return TaskInterface
     */
    public function registerResult(TaskManagerInterface $taskManager, ResultInterface $result): TaskInterface;

    /**
     * Static method to reconstruct a TaskInterface instance from its json representation
     * @param array $values
     * @return Task
     */
    public static function jsonDeserialize(array $values): TaskInterface;
}