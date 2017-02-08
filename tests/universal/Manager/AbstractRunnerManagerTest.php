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

namespace Teknoo\Tests\East\CodeRunner\Manager;

use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;

/**
 * Class AbstractRunnerManagerTest.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractRunnerManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * To get an instance of the class to test.
     *
     * @return RunnerManagerInterface
     */
    abstract public function buildManager(): RunnerManagerInterface;

    /**
     * @expectedException \Throwable
     */
    public function testRegisterMeBadTask()
    {
        $this->buildManager()->registerMe(new \stdClass());
    }

    public function testRegisterMeReturn()
    {
        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $this->buildManager()->registerMe($this->createMock(RunnerInterface::class))
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
            RunnerManagerInterface::class,
            $this->buildManager()->forgetMe($this->createMock(RunnerInterface::class))
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testPushResultBadRunner()
    {
        $this->buildManager()->pushResult(
            new \stdClass(),
            $this->createMock(TaskInterface::class),
            $this->createMock(ResultInterface::class)
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testPushResultBadTask()
    {
        $this->buildManager()->pushResult(
            $this->createMock(RunnerInterface::class),
            new \stdClass(),
            $this->createMock(ResultInterface::class)
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testPushResultBadResult()
    {
        $this->buildManager()->pushResult(
            $this->createMock(RunnerInterface::class),
            $this->createMock(TaskInterface::class),
            new \stdClass()
        );
    }

    public function testPushResultReturn()
    {
        $manager = $this->buildManager();
        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::any())->method('getIdentifier')->willReturn('runner');
        $result = $this->createMock(ResultInterface::class);
        $task = $this->createMock(TaskInterface::class);
        $task->expects(self::any())->method('getUrl')->willReturn('url');

        $runner->expects(self::any())
            ->method('canYouExecute')
            ->willReturnCallback(function (RunnerManagerInterface $manager, TaskInterface $task) use ($runner) {
                $manager->taskAccepted($runner, $task);

                return $runner;
            });

        $runner->expects(self::once())
            ->method('execute')
            ->with($manager, $task)
            ->willReturnSelf();

        $task = $this->createMock(TaskInterface::class);
        $task->expects(self::any())->method('getUrl')->willReturn('http://foo.bar');

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->registerMe(
                $runner
            )
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->executeForMeThisTask(
                $this->createMock(TaskManagerInterface::class),
                $task
            )
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->pushResult(
                $runner,
                $task,
                $result
            )
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testPushStatusBadRunner()
    {
        $this->buildManager()->pushStatus(
            new \stdClass(),
            $this->createMock(TaskInterface::class),
            $this->createMock(StatusInterface::class)
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testPushStatusBadTask()
    {
        $this->buildManager()->pushStatus(
            $this->createMock(RunnerInterface::class),
            new \stdClass(),
            $this->createMock(StatusInterface::class)
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testPushStatusBadStatus()
    {
        $this->buildManager()->pushStatus(
            $this->createMock(RunnerInterface::class),
            $this->createMock(TaskInterface::class),
            new \stdClass()
        );
    }

    public function testPushStatusReturn()
    {
        $manager = $this->buildManager();
        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::any())->method('getIdentifier')->willReturn('runner');
        $status = $this->createMock(StatusInterface::class);
        $task = $this->createMock(TaskInterface::class);
        $task->expects(self::any())->method('getUrl')->willReturn('url');

        $runner->expects(self::any())
            ->method('canYouExecute')
            ->willReturnCallback(function (RunnerManagerInterface $manager, TaskInterface $task) use ($runner) {
                $manager->taskAccepted($runner, $task);

                return $runner;
            });

        $runner->expects(self::once())
            ->method('execute')
            ->with($manager, $task)
            ->willReturnSelf();

        $task = $this->createMock(TaskInterface::class);
        $task->expects(self::any())->method('getUrl')->willReturn('http://foo.bar');

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->registerMe(
                $runner
            )
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->executeForMeThisTask(
                $this->createMock(TaskManagerInterface::class),
                $task
            )
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->pushStatus(
                $runner,
                $task,
                $status
            )
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testTaskAcceptedBadRunner()
    {
        $this->buildManager()->taskAccepted(
            new \stdClass(),
            $this->createMock(TaskInterface::class)
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testTaskAcceptedBadTask()
    {
        $this->buildManager()->taskAccepted(
            $this->createMock(RunnerInterface::class),
            new \stdClass()
        );
    }

    public function testTaskAcceptedReturn()
    {
        $manager = $this->buildManager();
        $task = $this->createMock(TaskInterface::class);

        $runner1 = $this->createMock(RunnerInterface::class);
        $runner1->expects(self::any())->method('getIdentifier')->willReturn('runner1');
        $runner1->expects(self::any())
            ->method('canYouExecute')
            ->willReturnCallback(function (RunnerManagerInterface $manager, TaskInterface $task) use ($runner1) {
                $manager->taskAccepted($runner1, $task);

                return $runner1;
            });

        $runner1->expects(self::once())
            ->method('execute')
            ->with($manager, $task)
            ->willReturnSelf();

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->registerMe(
                $runner1
            )
        );

        $runner2 = $this->createMock(RunnerInterface::class);
        $runner2->expects(self::any())->method('getIdentifier')->willReturn('runner2');
        $runner2->expects(self::never())
            ->method('canYouExecute');

        $runner2->expects(self::never())
            ->method('execute');

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->registerMe(
                $runner2
            )
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->executeForMeThisTask(
                $this->createMock(TaskManagerInterface::class),
                $task
            )
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testTaskRejectedBadRunner()
    {
        $this->buildManager()->taskRejected(
            new \stdClass(),
            $this->createMock(TaskInterface::class)
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testTaskRejectedBadTask()
    {
        $this->buildManager()->taskRejected(
            $this->createMock(RunnerInterface::class),
            new \stdClass()
        );
    }

    public function testTaskRejectedReturn()
    {
        $manager = $this->buildManager();
        $task = $this->createMock(TaskInterface::class);

        $runner1 = $this->createMock(RunnerInterface::class);
        $runner1->expects(self::any())->method('getIdentifier')->willReturn('runner1');
        $runner1->expects(self::once())
            ->method('canYouExecute')
            ->willReturnCallback(function (RunnerManagerInterface $manager, TaskInterface $task) use ($runner1) {
                $manager->taskRejected($runner1, $task);

                return $runner1;
            });

        $runner1->expects(self::never())
            ->method('execute');

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->registerMe(
                $runner1
            )
        );

        $runner2 = $this->createMock(RunnerInterface::class);
        $runner2->expects(self::any())->method('getIdentifier')->willReturn('runner2');
        $runner2->expects(self::once())
            ->method('canYouExecute')
            ->willReturnCallback(function (RunnerManagerInterface $manager, TaskInterface $task) use ($runner2) {
                $manager->taskAccepted($runner2, $task);

                return $runner2;
            });

        $runner2->expects(self::once())
            ->method('execute')
            ->with($manager, $task)
            ->willReturnSelf();

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->registerMe(
                $runner2
            )
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->executeForMeThisTask(
                $this->createMock(TaskManagerInterface::class),
                $task
            )
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testExecuteForMeThisTaskBadManager()
    {
        $this->buildManager()->executeForMeThisTask(
            new \stdClass(),
            $this->createMock(TaskInterface::class)
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testExecuteForMeThisTaskBadTask()
    {
        $this->buildManager()->executeForMeThisTask(
            $this->createMock(TaskManagerInterface::class),
            new \stdClass()
        );
    }

    /**
     * @expectedException \DomainException
     */
    public function testExecuteForMeThisTaskExceptionWhenTaskNotExecutableByAnyRunners()
    {
        $manager = $this->buildManager();
        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::any())->method('getIdentifier')->willReturn('runner');
        $task = $this->createMock(TaskInterface::class);
        $task->expects(self::any())->method('getUrl')->willReturn('task');

        $runner->expects(self::any())
            ->method('canYouExecute')
            ->willReturnCallback(function (RunnerManagerInterface $manager, TaskInterface $task) use ($runner) {
                $manager->taskRejected($runner, $task);

                return $runner;
            });

        $runner->expects(self::never())
            ->method('execute');

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->registerMe(
                $runner
            )
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->executeForMeThisTask(
                $this->createMock(TaskManagerInterface::class),
                $task
            )
        );
    }
}
