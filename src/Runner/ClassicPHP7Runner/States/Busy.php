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
namespace Teknoo\East\CodeRunnerBundle\Runner\ClassicPHP7Runner\States;

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\RemoteDockerPHP7Runner\ClassicPHP7Runner;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\East\CodeRunnerBundle\Task\Status;
use Teknoo\East\CodeRunnerBundle\Task\TextResult;
use Teknoo\States\State\AbstractState;

/**
 * State Busy
 * @mixin ClassicPHP7Runner
 */
class Busy extends AbstractState
{
    /**
     * {@inheritdoc}
     */
    private function doReset(): RunnerInterface
    {
        $this->currentTask = null;
        $this->currentResult = null;
        $this->currentManager = null;

        $this->updateStates();

        return $this;
    }

    private function doCanYouExecute(RunnerManagerInterface $manager, TaskInterface $task): RunnerInterface
    {
        $manager->taskRejected($this, $task);

        return $this;
    }

    private function run()
    {
        $this->currentManager->pushStatus($this, new Status('Executing'));
        $memoryUsageBefore = \memory_get_usage(true);
        $timeBefore = microtime(true);

        $output = '';$this->currentManager->pushStatus($this, new Status('Executed'));
        $error = '';
        try {
            $output = eval($this->currentTask->getCode()->getCode());
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        $memoryUsageAfter = \memory_get_usage(true);
        $timeAfter = microtime(true);

        $memoryUsage = $memoryUsageAfter - $memoryUsageBefore;
        $time = $timeAfter - $timeBefore;

        $this->currentResult = new TextResult($output, $error, $this->getVersion(), $memoryUsage, $time);

        $this->currentManager->pushStatus($this, new Status('Executed'));
        $this->currentManager->pushResult($this, $this->currentResult);
    }
}