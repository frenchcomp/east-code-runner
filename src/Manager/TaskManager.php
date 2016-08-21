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
namespace Teknoo\East\CodeRunnerBundle\Manager;

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;

class TaskManager implements TaskManagerInterface
{
    public function executeMe(TaskInterface $task): TaskManagerInterface
    {
        // TODO: Implement executeMe() method.
    }

    public function taskStatusIsUpdated(TaskInterface $task, StatusInterface $status): TaskManagerInterface
    {
        // TODO: Implement taskStatusIsUpdated() method.
    }

    public function taskResultIsUpdated(TaskInterface $task, ResultInterface $result): TaskManagerInterface
    {
        // TODO: Implement taskResultIsUpdated() method.
    }

    public function updateMyExecutionStatus(TaskInterface $task): TaskManagerInterface
    {
        // TODO: Implement updateMyExecutionStatus() method.
    }

    public function setMyExecutionResult(TaskInterface $task): TaskManagerInterface
    {
        // TODO: Implement setMyExecutionResult() method.
    }

    public function forgetMe(TaskInterface $task): TaskManagerInterface
    {
        // TODO: Implement forgetMe() method.
    }
}