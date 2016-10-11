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
namespace Teknoo\East\CodeRunnerBundle\Runner\RemoteDockerPHP7Runner\States;

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\RemoteDockerPHP7Runner\RemoteDockerPHP7Runner;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\States\State\AbstractState;
use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;

/**
 * State Busy
 * @mixin RemoteDockerPHP7Runner
 */
class Busy implements StateInterface
{
    use StateTrait;

    /**
     * {@inheritdoc}
     */
    private function doCanYouExecute()
    {
        return function (RunnerManagerInterface $manager, TaskInterface $task): RunnerInterface {
            $manager->taskRejected($this, $task);

            return $this;
        };
    }

    /**
     * {@inheritdoc}
     */
    private function doReset()
    {
        return function() : RunnerInterface {
            $this->currentTask = null;
            $this->currentResult = null;

            $this->updateStates();

            return $this;
        };
    }
}