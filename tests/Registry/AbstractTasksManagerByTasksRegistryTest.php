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
namespace Teknoo\Tests\East\CodeRunnerBundle\Registry;

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunnerBundle\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;

abstract class AbstractTasksManagerByTasksRegistryTest extends \PHPUnit_Framework_TestCase
{
    abstract public function buildRegistry(): TasksManagerByTasksRegistryInterface;

    /**
     * @exceptedException \InvalidArgumentException
     */
    public function testOffsetExistsInvalidArgument()
    {
        return isset($this->buildRegistry()[new \stdClass()]);
    }

    /**
     * @exceptedException \InvalidArgumentException
     */
    public function testOffsetGetInvalidArgument()
    {
        return $this->buildRegistry()[new \stdClass()];
    }

    /**
     * @exceptedException \InvalidArgumentException
     */
    public function testOffsetSetInvalidArgument()
    {
        $this->buildRegistry()[new \stdClass()] = $this->createMock(TaskManagerInterface::class);
    }

    /**
     * @exceptedException \InvalidArgumentException
     */
    public function testOffsetUnsetInvalidArgument()
    {
        unset($this->buildRegistry()[new \stdClass()]);
    }

    public function testArrayAccessBehavior()
    {
        $manager1 = $this->createMock(TaskManagerInterface::class);
        $manager2 = $this->createMock(TaskManagerInterface::class);
        $manager3 = $this->createMock(TaskManagerInterface::class);

        $task1 = $this->createMock(TaskInterface::class);
        $task2 = $this->createMock(TaskInterface::class);
        $task3 = $this->createMock(TaskInterface::class);

        $registry = $this->buildRegistry();

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

        unset($registry[$task2]);
        $registry[$task3] = $manager1;

        self::assertTrue(isset($registry[$task1]));
        self::assertFalse(isset($registry[$task2]));
        self::assertTrue(isset($registry[$task3]));

        self::assertEquals($manager1, $registry[$task1]);
        self::assertNull($registry[$task2]);
        self::assertEquals($manager1, $registry[$task3]);
    }

    public function testClearAll()
    {
        $registry = $this->buildRegistry();

        $task = $this->createMock(TaskInterface::class);
        $task->expects(self::any())
            ->method('getUrl')
            ->willReturn('fooBar');

        $manager = $this->createMock(TaskManagerInterface::class);

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