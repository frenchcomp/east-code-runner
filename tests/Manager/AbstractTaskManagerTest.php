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

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;

abstract class AbstractTaskManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * To get an instance of the class to test
     * @return TaskManagerInterface
     */
    abstract public function buildManager(): TaskManagerInterface;

    /**
     * @exceptedException \Throwable
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

    public function testExecuteMeMustCallRegisterTaskManagerExecuting()
    {
        $manager = $this->buildManager();
        $task = $this->createMock(TaskInterface::class);

        $task->expects(self::once())
            ->method('registerTaskManagerExecuting')
            ->with(new \PHPUnit_Framework_Constraint_Not(self::isEmpty()), $manager)
            ->willReturnSelf();

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->executeMe($task)
        );
    }

    /**
     * @exceptedException \Throwable
     */
    public function testTaskStatusIsUpdatedBadTask()
    {
        $this->buildManager()->taskStatusIsUpdated(
            new \stdClass(),
            $this->createMock(StatusInterface::class)
        );
    }

    /**
     * @exceptedException \Throwable
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
     * @exceptedException \Throwable
     */
    public function testTaskResultIsUpdatedBadTask()
    {
        $this->buildManager()->taskResultIsUpdated(
            new \stdClass(),
            $this->createMock(ResultInterface::class)
        );
    }

    /**
     * @exceptedException \Throwable
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
     * @exceptedException \Throwable
     */
    public function testUpdateMyExecutionStatusBadTask()
    {
        $this->buildManager()->updateMyExecutionStatus(new \stdClass());
    }

    /**
     * @exceptedException \DomainException
     */
    public function testUpdateMyExecutionStatusExceptionWithUnknownTask()
    {
        $manager = $this->buildManager();
        $task = $this->createMock(TaskInterface::class);

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->updateMyExecutionStatus($task)
        );
    }
    public function testUpdateMyExecutionStatusExceptionWithUnknownTaskAfterForget()
    {
        $manager = $this->buildManager();
        $task = $this->createMock(TaskInterface::class);

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->executeMe($task)
        );

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->forgetMe($task)
        );

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->updateMyExecutionStatus($task)
        );
    }

    public function testUpdateMyExecutionStatusReturn()
    {
        $manager = $this->buildManager();
        $task = $this->createMock(TaskInterface::class);

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->executeMe($task)
        );

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->updateMyExecutionStatus($task)
        );
    }

    /**
     * @exceptedException \Throwable
     */
    public function testSetMyExecutionResultBadTask()
    {
        $this->buildManager()->setMyExecutionResult(new \stdClass());
    }

    /**
     * @exceptedException \DomainException
     */
    public function testSetMyExecutionResultExceptionWithUnknownTask()
    {
        $manager = $this->buildManager();
        $task = $this->createMock(TaskInterface::class);

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->setMyExecutionResult($task)
        );
    }

    /**
     * @exceptedException \DomainException
     */
    public function testSetMyExecutionResultExceptionWithUnknownTaskAfterForget()
    {
        $manager = $this->buildManager();
        $task = $this->createMock(TaskInterface::class);

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->executeMe($task)
        );

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->forgetMe($task)
        );

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->setMyExecutionResult($task)
        );
    }

    public function testSetMyExecutionResultReturn()
    {
        $manager = $this->buildManager();
        $task = $this->createMock(TaskInterface::class);

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->executeMe($task)
        );

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->setMyExecutionResult($task)
        );
    }

    public function testSetMyExecutionResultMustCallRegisterResultIfThereAreResult()
    {
        $manager = $this->buildManager();
        $task = $this->createMock(TaskInterface::class);
        $result = $this->createMock(ResultInterface::class);

        $task->expects(self::once())
            ->method('setMyExecutionResult')
            ->with($manager, $result)
            ->willReturnSelf();

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->executeMe($task)
        );

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->taskResultIsUpdated($task, $result)
        );

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->setMyExecutionResult($task)
        );
    }

    public function testSetMyExecutionResultMustNotCallRegisterResultIfThereAreNotResult()
    {
        $manager = $this->buildManager();
        $task = $this->createMock(TaskInterface::class);
        $result = $this->createMock(ResultInterface::class);

        $task->expects(self::never())
            ->method('setMyExecutionResult')
            ->with($manager, $result)
            ->willReturnSelf();

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->executeMe($task)
        );

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->setMyExecutionResult($task)
        );
    }

    /**
     * @exceptedException \Throwable
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