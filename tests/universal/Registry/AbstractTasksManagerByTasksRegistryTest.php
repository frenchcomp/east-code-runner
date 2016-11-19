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
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
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

abstract class AbstractTasksManagerByTasksRegistryTest extends \PHPUnit_Framework_TestCase
{
    abstract public function buildRegistry(): TasksManagerByTasksRegistryInterface;

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOffsetExistsInvalidArgument()
    {
        return isset($this->buildRegistry()[new \stdClass()]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOffsetGetInvalidArgument()
    {
        return $this->buildRegistry()[new \stdClass()];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOffsetSetInvalidArgument()
    {
        $this->buildRegistry()[new \stdClass()] = $this->createMock(TaskManagerInterface::class);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOffsetUnsetInvalidArgument()
    {
        unset($this->buildRegistry()[new \stdClass()]);
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
        $task1->expects(self::any())->method('getUrl')->willReturn('https://teknoo.software/task1');
        $task2 = $this->createMock(TaskInterface::class);
        $task2->expects(self::any())->method('getUrl')->willReturn('https://teknoo.software/task2');
        $task3 = $this->createMock(TaskInterface::class);
        $task3->expects(self::any())->method('getUrl')->willReturn('https://teknoo.software/task3');

        $registry = $this->buildRegistry();
        $registry->addTaskManager($manager1);
        $registry->addTaskManager($manager2);
        $registry->addTaskManager($manager3);

        self::assertFalse(isset($registry[$task1]));
        self::assertFalse(isset($registry[$task2]));
        self::assertFalse(isset($registry[$task3]));

        self::assertNull($registry[$task1]);
        self::assertNull($registry[$task2]);
        self::assertNull($registry[$task3]);

        $registry[$task1] = $manager1;
        $registry[$task2] = $manager2;
        $registry[$task3] = $manager3;

        self::assertTrue(isset($registry[$task1]));
        self::assertTrue(isset($registry[$task2]));
        self::assertTrue(isset($registry[$task3]));

        self::assertEquals($manager1, $registry[$task1]);
        self::assertEquals($manager2, $registry[$task2]);
        self::assertEquals($manager3, $registry[$task3]);

        $registry[$task2] = $manager3;
        self::assertEquals($manager3, $registry[$task2]);

        unset($registry[$task2]);
        $registry[$task3] = $manager1;

        self::assertTrue(isset($registry[$task1]));
        self::assertFalse(isset($registry[$task2]));
        self::assertTrue(isset($registry[$task3]));

        self::assertEquals($manager1, $registry[$task1]);
        self::assertNull($registry[$task2]);
        self::assertEquals($manager1, $registry[$task3]);
    }

    /**
     * @expectedException \DomainException
     */
    public function testArrayAccessBehaviorUnknownManager()
    {
        $manager1 = $this->createMock(TaskManagerInterface::class);
        $manager1->expects(self::any())->method('getIdentifier')->willReturn('manager1');

        $task1 = $this->createMock(TaskInterface::class);
        $task1->expects(self::any())->method('getUrl')->willReturn('https://teknoo.software/task1');

        $registry = $this->buildRegistry();

        self::assertFalse(isset($registry[$task1]));

        self::assertNull($registry[$task1]);

        $registry[$task1] = $manager1;

        self::assertTrue(isset($registry[$task1]));

        $registry[$task1];
    }

    public function testClearAll()
    {
        $registry = $this->buildRegistry();

        $task = $this->createMock(TaskInterface::class);
        $task->expects(self::any())
            ->method('getUrl')
            ->willReturn('fooBar');

        $manager = $this->createMock(TaskManagerInterface::class);
        $registry->addTaskManager($manager);

        self::assertFalse(isset($registry[$task]));

        $registry[$task] = $manager;

        self::assertTrue(isset($registry[$task]));

        self::assertInstanceOf(
            TasksManagerByTasksRegistryInterface::class,
            $registry->clearAll()
        );

        self::assertFalse(isset($registry[$task]));
    }
}