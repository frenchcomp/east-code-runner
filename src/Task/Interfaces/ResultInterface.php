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

use Teknoo\Immutable\ImmutableInterface;

/**
 * Interface to define, as value object, the result for task
 */
interface ResultInterface extends ImmutableInterface
{
    /**
     * Standard output of the task.
     *
     * @return string
     */
    public function getOutput(): string;

    /**
     * Error output of the task.
     *
     * @return string
     */
    public function getErrors(): string;

    /**
     * To know runner's version where the task has been executed.
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * To know the memory size used to execute the task, in octet. Ignore compilation usage for compiled language.
     *
     * @return int
     */
    public function getMemorySize(): int;

    /**
     * To know the time needed to execute the task, in second. Ignore compilation time for compiled language.
     *
     * @return int
     */
    public function getTimeExecution(): int;
}