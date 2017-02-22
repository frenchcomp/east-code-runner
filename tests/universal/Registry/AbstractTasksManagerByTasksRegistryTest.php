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

use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\Foundation\Promise\PromiseInterface;

/**
 * Class AbstractTasksManagerByTasksRegistryTest.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractTasksManagerByTasksRegistryTest extends \PHPUnit_Framework_TestCase
{
    abstract public function buildRegistry(): TasksManagerByTasksRegistryInterface;

    /**
     * @expectedException \Throwable
     */
    public function testGetInvalidTask()
    {
        return $this->buildRegistry()->get(new \stdClass(), $this->createMock(PromiseInterface::class));
    }
    /**
     * @expectedException \Throwable
     */
    public function testGetInvalidPromise()
    {
        return $this->buildRegistry()->get($this->createMock(TaskInterface::class), new \stdClass());
    }

    /**
     * @expectedException \Throwable
     */
    public function testRegisterInvalidTask()
    {
        $this->buildRegistry()->register(new \stdClass(), $this->createMock(TaskManagerInterface::class));
    }

    /**
     * @expectedException \Throwable
     */
    public function testRegisterInvalidTaskManager()
    {
        $this->buildRegistry()->register($this->createMock(TaskInterface::class), new \stdClass());
    }

    /**
     * @expectedException \Throwable
     */
    public function testRemoveInvalidArgument()
    {
        $this->buildRegistry()->remove(new \stdClass());
    }

    public function testArrayAccessBehavior()
    {
        $manager1 = $this->createMock(TaskManagerInterface::class);
        $manager1->expects(self::any())->method('getIdentifier')->willReturn('manager1');
        $manager2 = $this->createMock(TaskManagerInterface::class);
        $manager2->expects(self::any())->method('getIdentifier')->willReturn('manager2');
        $manager3 = $this->createMock(TaskManagerInterface::class);
        $manager3->expects(self::any())->method('getIdentifier')->willReturn('manager3');

        $task1 = $this->createMock(TaskInterface::class);
        $task1->expects(self::any())->method('getId')->willReturn('https://teknoo.software/task1');
        $task2 = $this->createMock(TaskInterface::class);
        $task2->expects(self::any())->method('getId')->willReturn('https://teknoo.software/task2');
        $task3 = $this->createMock(TaskInterface::class);
        $task3->expects(self::any())->method('getId')->willReturn('https://teknoo.software/task3');

        $registry = $this->buildRegistry();
        $registry->addTaskManager($manager1);
        $registry->addTaskManager($manager2);
        $registry->addTaskManager($manager3);

        $promise1 = $this->createMock(PromiseInterface::class);
        $promise1->expects(self::once())->method('fail');
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->get($task1, $promise1));

        $promise2 = $this->createMock(PromiseInterface::class);
        $promise2->expects(self::once())->method('fail');
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->get($task2, $promise2));

        $promise3 = $this->createMock(PromiseInterface::class);
        $promise3->expects(self::once())->method('fail');
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->get($task3, $promise3));


        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->register($task1, $manager1));
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->register($task2, $manager2));
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->register($task3, $manager3));

        $promise4 = $this->createMock(PromiseInterface::class);
        $promise4->expects(self::once())->method('success')->willReturnCallback(
            function ($manager) use ($manager1, $promise4) {
                self::assertInstanceOf(TaskManagerInterface::class, $manager);
                self::assertEquals($manager->getIdentifier(), $manager1->getIdentifier());

                return $promise4;
            }
        );
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->get($task1, $promise4));

        $promise5 = $this->createMock(PromiseInterface::class);
        $promise5->expects(self::once())->method('success')->willReturnCallback(
            function ($manager) use ($manager2, $promise5) {
                self::assertInstanceOf(TaskManagerInterface::class, $manager);
                self::assertEquals($manager->getIdentifier(), $manager2->getIdentifier());

                return $promise5;
            }
        );
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->get($task2, $promise5));

        $promise6 = $this->createMock(PromiseInterface::class);
        $promise6->expects(self::once())->method('success')->willReturnCallback(
            function ($manager) use ($manager3, $promise6) {
                self::assertInstanceOf(TaskManagerInterface::class, $manager);
                self::assertEquals($manager->getIdentifier(), $manager3->getIdentifier());

                return $promise6;
            }
        );
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->get($task3, $promise6));


        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->register($task2, $manager3));
        $promise7 = $this->createMock(PromiseInterface::class);
        $promise7->expects(self::once())->method('success')->willReturnCallback(
            function ($manager) use ($manager3, $promise7) {
                self::assertInstanceOf(TaskManagerInterface::class, $manager);
                self::assertEquals($manager->getIdentifier(), $manager3->getIdentifier());

                return $promise7;
            }
        );
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->get($task2, $promise7));

        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->remove($task2));
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->register($task3, $manager1));

        $promise8 = $this->createMock(PromiseInterface::class);
        $promise8->expects(self::once())->method('success')->willReturnCallback(
            function ($manager) use ($manager1, $promise8) {
                self::assertInstanceOf(TaskManagerInterface::class, $manager);
                self::assertEquals($manager->getIdentifier(), $manager1->getIdentifier());

                return $promise8;
            }
        );
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->get($task1, $promise8));

        $promise9 = $this->createMock(PromiseInterface::class);
        $promise9->expects(self::once())->method('fail');
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->get($task2, $promise9));

        $promise10 = $this->createMock(PromiseInterface::class);
        $promise10->expects(self::once())->method('success')->willReturnCallback(
            function ($manager) use ($manager1, $promise10) {
                self::assertInstanceOf(TaskManagerInterface::class, $manager);
                self::assertEquals($manager->getIdentifier(), $manager1->getIdentifier());

                return $promise10;
            }
        );
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->get($task3, $promise10));
    }

    public function testArrayAccessBehaviorUnknownManager()
    {
        $manager1 = $this->createMock(TaskManagerInterface::class);
        $manager1->expects(self::any())->method('getIdentifier')->willReturn('manager1');

        $task1 = $this->createMock(TaskInterface::class);
        $task1->expects(self::any())->method('getId')->willReturn('https://teknoo.software/task1');

        $registry = $this->buildRegistry();

        $promise1 = $this->createMock(PromiseInterface::class);
        $promise1->expects(self::once())->method('fail');
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->get($task1, $promise1));

        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->register($task1, $manager1));

        $promise2 = $this->createMock(PromiseInterface::class);
        $promise2->expects(self::once())->method('fail')->willReturnSelf();
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->get($task1, $promise2));

    }

    public function testClearAll()
    {
        $registry = $this->buildRegistry();

        $task = $this->createMock(TaskInterface::class);
        $task->expects(self::any())
            ->method('getId')
            ->willReturn('fooBar');

        $manager = $this->createMock(TaskManagerInterface::class);
        $registry->addTaskManager($manager);

        $promise1 = $this->createMock(PromiseInterface::class);
        $promise1->expects(self::once())->method('fail');
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->get($task, $promise1));

        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->register($task, $manager));

        $promise2 = $this->createMock(PromiseInterface::class);
        $promise2->expects(self::once())->method('success')->willReturnSelf();
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->get($task, $promise2));

        self::assertInstanceOf(
            TasksManagerByTasksRegistryInterface::class,
            $registry->clearAll()
        );

        $promise3 = $this->createMock(PromiseInterface::class);
        $promise3->expects(self::once())->method('fail');
        self::assertInstanceOf(TasksManagerByTasksRegistryInterface::class, $registry->get($task, $promise3));
    }
}
