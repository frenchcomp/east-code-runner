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
namespace Teknoo\Tests\East\CodeRunner\Manager;

use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;

abstract class AbstractTaskManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * To get an instance of the class to test
     * @return TaskManagerInterface
     */
    abstract public function buildManager(): TaskManagerInterface;

    public function testGetIdentifierReturn()
    {
        self::assertInternalType(
            'string',
            $this->buildManager()->getIdentifier()
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testExecuteMeBadTask()
    {
        $this->buildManager()->executeMe(new \stdClass());
    }

    public function testExecuteMeReturn()
    {
        self::assertInstanceOf(
            TaskManagerInterface::class,
            $this->buildManager()->executeMe($this->createMock(TaskInterface::class))
        );
    }

    public function testExecuteMeMustCallregisterUrl()
    {
        $manager = $this->buildManager();
        $task = $this->createMock(TaskInterface::class);

        $task->expects(self::once())
            ->method('registerUrl')
            ->with(new \PHPUnit_Framework_Constraint_Not(self::isEmpty()))
            ->willReturnSelf();

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->executeMe($task)
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testTaskStatusIsUpdatedBadTask()
    {
        $this->buildManager()->taskStatusIsUpdated(
            new \stdClass(),
            $this->createMock(StatusInterface::class)
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testTaskStatusIsUpdatedBadStatus()
    {
        $this->buildManager()->taskStatusIsUpdated(
            $this->createMock(TaskInterface::class),
            new \stdClass()
        );
    }

    public function testTaskStatusIsUpdatedReturn()
    {
        self::assertInstanceOf(
            TaskManagerInterface::class,
            $this->buildManager()->taskStatusIsUpdated(
                $this->createMock(TaskInterface::class),
                $this->createMock(StatusInterface::class)
            )
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testTaskResultIsUpdatedBadTask()
    {
        $this->buildManager()->taskResultIsUpdated(
            new \stdClass(),
            $this->createMock(ResultInterface::class)
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testTaskResultIsUpdatedBadResult()
    {
        $this->buildManager()->taskResultIsUpdated(
            $this->createMock(TaskInterface::class),
            new \stdClass()
        );
    }

    public function testTaskResultIsUpdatedReturn()
    {
        self::assertInstanceOf(
            TaskManagerInterface::class,
            $this->buildManager()->taskResultIsUpdated(
                $this->createMock(TaskInterface::class),
                $this->createMock(ResultInterface::class)
            )
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testForgetMeBadTask()
    {
        $this->buildManager()->forgetMe(new \stdClass());
    }

    public function testForgetMeReturn()
    {
        self::assertInstanceOf(
            TaskManagerInterface::class,
            $this->buildManager()->forgetMe($this->createMock(TaskInterface::class))
        );
    }
}