<?php

/**
 * East CodeRunner.
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

namespace Teknoo\Tests\East\CodeRunner\Registry;

use Teknoo\East\CodeRunner\Registry\Interfaces\TasksByRunnerRegistryInterface;
use Teknoo\East\CodeRunner\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\Foundation\Promise\PromiseInterface;

/**
 * Class AbstractTasksByRunnerRegistryTest.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractTasksByRunnerRegistryTest extends \PHPUnit_Framework_TestCase
{
    abstract public function buildRegistry(): TasksByRunnerRegistryInterface;

    /**
     * @expectedException \Throwable
     */
    public function testGetInvalidId()
    {
        return $this->buildRegistry()->get(new \stdClass(), $this->createMock(PromiseInterface::class));
    }
    /**
     * @expectedException \Throwable
     */
    public function testGetInvalidPromise()
    {
        return $this->buildRegistry()->get($this->createMock(RunnerInterface::class), new \stdClass());
    }

    /**
     * @expectedException \Throwable
     */
    public function testRegisterInvalidRunner()
    {
        $this->buildRegistry()->register(new \stdClass(), $this->createMock(TaskInterface::class));
    }

    /**
     * @expectedException \Throwable
     */
    public function testRegisterInvalidRunnerTask()
    {
        $this->buildRegistry()->register($this->createMock(RunnerInterface::class), new \stdClass());
    }

    /**
     * @expectedException \Throwable
     */
    public function testRemoveInvalidRunner()
    {
        $this->buildRegistry()->remove(new \stdClass());
    }

    public function testArrayAccessBehavior()
    {
        $task1 = $this->createMock(TaskInterface::class);
        $task1->expects(self::any())->method('getUrl')->willReturn('https://teknoo.software/task1');
        $task2 = $this->createMock(TaskInterface::class);
        $task2->expects(self::any())->method('getUrl')->willReturn('https://teknoo.software/task2');
        $task3 = $this->createMock(TaskInterface::class);
        $task3->expects(self::any())->method('getUrl')->willReturn('https://teknoo.software/task3');

        $runner1 = $this->createMock(RunnerInterface::class);
        $runner1->expects(self::any())->method('getIdentifier')->willReturn('runner1');
        $runner2 = $this->createMock(RunnerInterface::class);
        $runner2->expects(self::any())->method('getIdentifier')->willReturn('runner2');
        $runner3 = $this->createMock(RunnerInterface::class);
        $runner3->expects(self::any())->method('getIdentifier')->willReturn('runner3');

        $registry = $this->buildRegistry();

        $promise1 = $this->createMock(PromiseInterface::class);
        $promise1->expects(self::once())->method('fail');
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->get($runner1, $promise1));

        $promise2 = $this->createMock(PromiseInterface::class);
        $promise2->expects(self::once())->method('fail');
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->get($runner2, $promise2));

        $promise3 = $this->createMock(PromiseInterface::class);
        $promise3->expects(self::once())->method('fail');
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->get($runner3, $promise3));

        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->register($runner1, $task1));
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->register($runner2, $task2));
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->register($runner3, $task3));

        $promise4 = $this->createMock(PromiseInterface::class);
        $promise4->expects(self::once())->method('success')->willReturnCallback(
            function ($task) use ($task1, $promise4) {
                self::assertInstanceOf(TaskInterface::class, $task);
                self::assertEquals($task->getUrl(), $task1->getUrl());

                return $promise4;
            }
        );
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->get($runner1, $promise4));

        $promise5 = $this->createMock(PromiseInterface::class);
        $promise5->expects(self::once())->method('success')->willReturnCallback(
            function ($task) use ($task2, $promise5) {
                self::assertInstanceOf(TaskInterface::class, $task);
                self::assertEquals($task->getUrl(), $task2->getUrl());

                return $promise5;
            }
        );
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->get($runner2, $promise5));

        $promise6 = $this->createMock(PromiseInterface::class);
        $promise6->expects(self::once())->method('success')->willReturnCallback(
            function ($task) use ($task3, $promise6) {
                self::assertInstanceOf(TaskInterface::class, $task);
                self::assertEquals($task->getUrl(), $task3->getUrl());

                return $promise6;
            }
        );
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->get($runner3, $promise6));

        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->register($runner2, $task3));
        $promise7 = $this->createMock(PromiseInterface::class);
        $promise7->expects(self::once())->method('success')->willReturnCallback(
            function ($task) use ($task3, $promise7) {
                self::assertInstanceOf(TaskInterface::class, $task);
                self::assertEquals($task->getUrl(), $task3->getUrl());

                return $promise7;
            }
        );
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->get($runner2, $promise7));

        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->remove($runner2));
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->register($runner3, $task1));

        $promise8 = $this->createMock(PromiseInterface::class);
        $promise8->expects(self::once())->method('success')->willReturnCallback(
            function ($task) use ($task1, $promise8) {
                self::assertInstanceOf(TaskInterface::class, $task);
                self::assertEquals($task->getUrl(), $task1->getUrl());

                return $promise8;
            }
        );
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->get($runner1, $promise8));

        $promise9 = $this->createMock(PromiseInterface::class);
        $promise9->expects(self::once())->method('fail');
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->get($runner2, $promise9));

        $promise10 = $this->createMock(PromiseInterface::class);
        $promise10->expects(self::once())->method('success')->willReturnCallback(
            function ($task) use ($task1, $promise10) {
                self::assertInstanceOf(TaskInterface::class, $task);
                self::assertEquals($task->getUrl(), $task1->getUrl());

                return $promise10;
            }
        );
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->get($runner3, $promise10));
    }

    public function testClearAll()
    {
        $registry = $this->buildRegistry();

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::any())
            ->method('getIdentifier')
            ->willReturn('fooBar');

        $task = $this->createMock(TaskInterface::class);

        $promise1 = $this->createMock(PromiseInterface::class);
        $promise1->expects(self::once())->method('fail');
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->get($runner, $promise1));

        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->register($runner, $task));

        $promise2 = $this->createMock(PromiseInterface::class);
        $promise2->expects(self::once())->method('success')->willReturnSelf();
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->get($runner, $promise2));

        self::assertInstanceOf(
            TasksByRunnerRegistryInterface::class,
            $registry->clearAll()
        );

        $promise3 = $this->createMock(PromiseInterface::class);
        $promise3->expects(self::once())->method('fail');
        self::assertInstanceOf(TasksByRunnerRegistryInterface::class, $registry->get($runner, $promise3));
    }
}
