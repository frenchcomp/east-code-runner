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

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;

class RunnerManager implements RunnerManagerInterface
{
    public function registerMe(RunnerInterface $runner): RunnerManagerInterface
    {
        // TODO: Implement registerMe() method.
    }

    public function forgetMe(RunnerInterface $runner): RunnerManagerInterface
    {
        // TODO: Implement forgetMe() method.
    }

    public function pushResult(RunnerInterface $runner, ResultInterface $result): RunnerManagerInterface
    {
        // TODO: Implement pushResult() method.
    }

    public function taskAccepted(RunnerInterface $runner, TaskInterface $task): RunnerManagerInterface
    {
        // TODO: Implement taskAccepted() method.
    }

    public function taskRejected(RunnerInterface $runner, TaskInterface $task): RunnerManagerInterface
    {
        // TODO: Implement taskRejected() method.
    }

    public function executeForMeThisTask(TaskManagerInterface $taskManager, TaskInterface $task): RunnerManagerInterface
    {
        // TODO: Implement executeForMeThisTask() method.
    }
}