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
namespace Teknoo\Tests\East\CodeRunnerBundle\Manager;

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunnerBundle\Manager\RunnerManager\RunnerManager;
use Teknoo\East\CodeRunnerBundle\Registry\Interfaces\TasksByRunnerRegistryInterface;
use Teknoo\East\CodeRunnerBundle\Registry\Interfaces\TasksManagerByTasksRegistryInterface;

/**
 * Test RunnerManagerTest
 * @covers Teknoo\East\CodeRunnerBundle\Manager\RunnerManager\RunnerManager
 * @covers Teknoo\East\CodeRunnerBundle\Manager\RunnerManager\Factory
 * @covers Teknoo\East\CodeRunnerBundle\Manager\RunnerManager\States\Running
 * @covers Teknoo\East\CodeRunnerBundle\Manager\RunnerManager\States\Selecting
 */
class RunnerManagerTest extends AbstractRunnerManagerTest
{
    /**
     * @var TasksByRunnerRegistryInterface
     */
    private $tasksByRunner;

    /**
     * @var TasksManagerByTasksRegistryInterface
     */
    private $tasksManagerByTasks;

    /**
     * @return TasksByRunnerRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getTasksByRunnerMock(): TasksByRunnerRegistryInterface
    {
        if (!$this->tasksByRunner instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->tasksByRunner = $this->createMock(TasksByRunnerRegistryInterface::class);

            $repository = [];
            $this->tasksByRunner
                ->expects(self::any())
                ->method('offsetExists')
                ->willReturnCallback(function ($name) use (&$repository) {
                   return isset($repository[$name]);
                });

            $this->tasksByRunner
                ->expects(self::any())
                ->method('offsetGet')
                ->willReturnCallback(function ($name) use (&$repository) {
                    return $repository[$name];
                });

            $this->tasksByRunner
                ->expects(self::any())
                ->method('offsetSet')
                ->willReturnCallback(function ($name, $value) use (&$repository) {
                    $repository[$name] = $value;
                });

            $this->tasksByRunner
                ->expects(self::any())
                ->method('offsetUnset')
                ->willReturnCallback(function ($name) use (&$repository) {
                    unset($repository[$name]);
                });
        }

        return $this->tasksByRunner;
    }

    /**
     * @return TasksManagerByTasksRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getTasksManagerByTasksMock(): TasksManagerByTasksRegistryInterface
    {
        if (!$this->tasksManagerByTasks instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->tasksManagerByTasks = $this->createMock(TasksManagerByTasksRegistryInterface::class);

            $repository = [];
            $this->tasksManagerByTasks
                ->expects(self::any())
                ->method('offsetExists')
                ->willReturnCallback(function ($name) use (&$repository) {
                    return isset($repository[$name]);
                });

            $this->tasksManagerByTasks
                ->expects(self::any())
                ->method('offsetGet')
                ->willReturnCallback(function ($name) use (&$repository) {
                    return $repository[$name];
                });

            $this->tasksManagerByTasks
                ->expects(self::any())
                ->method('offsetSet')
                ->willReturnCallback(function ($name, $value) use (&$repository) {
                    $repository[$name] = $value;
                });

            $this->tasksManagerByTasks
                ->expects(self::any())
                ->method('offsetUnset')
                ->willReturnCallback(function ($name) use (&$repository) {
                    unset($repository[$name]);
                });
        }

        return $this->tasksManagerByTasks;
    }

    /**
     * @return RunnerManagerInterface|RunnerManager
     */
    public function buildManager(): RunnerManagerInterface
    {
        return new RunnerManager(
            $this->getTasksByRunnerMock(),
            $this->getTasksManagerByTasksMock()
        );
    }
}