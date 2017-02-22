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

use Teknoo\East\CodeRunner\Registry\Interfaces\TasksStandbyRegistryInterface;
use Teknoo\East\CodeRunner\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\Foundation\Promise\PromiseInterface;

/**
 * Class AbstractTasksStandbyRegistryTest.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractTasksStandbyRegistryTest extends \PHPUnit_Framework_TestCase
{
    abstract public function buildRegistry(): TasksStandbyRegistryInterface;

    /**
     * @expectedException \Throwable
     */
    public function testEnqueueInvalidRunner()
    {
        return $this->buildRegistry()->enqueue(
            new \stdClass(),
            $this->createMock(TaskInterface::class)
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testEnqueueInvalidTask()
    {
        return $this->buildRegistry()->enqueue(
            $this->createMock(RunnerInterface::class),
            new \stdClass()
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testDequeueInvalidRunner()
    {
        $this->buildRegistry()->dequeue(new \stdClass(), $this->createMock(PromiseInterface::class));
    }

    /**
     * @expectedException \Throwable
     */
    public function testDequeueInvalidPromise()
    {
        $this->buildRegistry()->dequeue($this->createMock(RunnerInterface::class), new \stdClass());
    }

    public function testQueueDequeueBehavior()
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

        $registry = $this->buildRegistry();

        $promise1 = $this->createMock(PromiseInterface::class);
        $promise1->expects(self::once())->method('fail')->willReturnSelf();
        self::assertInstanceOf(
            TasksStandbyRegistryInterface::class,
            $registry->dequeue(
                $runner1,
                $promise1
            )
        );

        $promise2 = $this->createMock(PromiseInterface::class);
        $promise2->expects(self::once())->method('fail')->willReturnSelf();
        self::assertInstanceOf(
            TasksStandbyRegistryInterface::class,
            $registry->dequeue($runner2, $promise2)
        );

        self::assertInstanceOf(
            TasksStandbyRegistryInterface::class,
            $registry->enqueue($runner1, $task1)
        );

        self::assertInstanceOf(
            TasksStandbyRegistryInterface::class,
            $registry->enqueue($runner2, $task2)
        );

        self::assertInstanceOf(
            TasksStandbyRegistryInterface::class,
            $registry->enqueue($runner1, $task3)
        );

        $promise3 = $this->createMock(PromiseInterface::class);
        $promise3->expects(self::once())->method('success')->with($task1)->willReturnSelf();
        self::assertInstanceOf(
            TasksStandbyRegistryInterface::class,
            $registry->dequeue($runner1, $promise3)
        );

        $promise4 = $this->createMock(PromiseInterface::class);
        $promise4->expects(self::once())->method('success')->with($task1)->willReturnSelf();
        self::assertInstanceOf(
            TasksStandbyRegistryInterface::class,
            $registry->dequeue($runner1, $promise4)
        );

        $promise5 = $this->createMock(PromiseInterface::class);
        $promise5->expects(self::once())->method('success')->with($task1)->willReturnSelf();
        self::assertInstanceOf(
            TasksStandbyRegistryInterface::class,
            $registry->dequeue($runner2, $promise5)
        );

        $promise6 = $this->createMock(PromiseInterface::class);
        $promise6->expects(self::once())->method('fail')->willReturnSelf();
        self::assertInstanceOf(
            TasksStandbyRegistryInterface::class,
            $registry->dequeue($runner1, $promise6)
        );

        $promise7 = $this->createMock(PromiseInterface::class);
        $promise7->expects(self::once())->method('fail')->willReturnSelf();
        self::assertInstanceOf(
            TasksStandbyRegistryInterface::class,
            $registry->dequeue($runner2, $promise7)
        );
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
        $promise1->expects(self::once())->method('fail')->willReturnSelf();
        self::assertInstanceOf(
            TasksStandbyRegistryInterface::class,
            $registry->dequeue($runner, $promise1)
        );

        self::assertInstanceOf(
            TasksStandbyRegistryInterface::class,
            $registry->enqueue($runner, $task)
        );

        self::assertInstanceOf(
            TasksStandbyRegistryInterface::class,
            $registry->clearAll()
        );

        $promise2 = $this->createMock(PromiseInterface::class);
        $promise2->expects(self::once())->method('fail')->willReturnSelf();
        self::assertInstanceOf(
            TasksStandbyRegistryInterface::class,
            $registry->dequeue($runner, $promise2)
        );
    }
}
