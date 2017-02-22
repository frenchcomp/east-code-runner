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

use Psr\Log\LoggerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Manager\RunnerManager\RunnerManager;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksByRunnerRegistryInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksStandbyRegistryInterface;
use Teknoo\East\CodeRunner\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\Foundation\Promise\PromiseInterface;

/**
 * Test RunnerManagerTest.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\East\CodeRunner\Manager\RunnerManager\RunnerManager
 * @covers \Teknoo\East\CodeRunner\Manager\RunnerManager\States\Running
 * @covers \Teknoo\East\CodeRunner\Manager\RunnerManager\States\Selecting
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
     * @var TasksStandbyRegistryInterface
     */
    private $tasksStandbyRegistry;

    /**
     * @var LoggerInterface
     */
    private $logger;

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
                ->method('get')
                ->willReturnCallback(function (RunnerInterface $name, PromiseInterface $promise) use (&$repository) {
                    if (isset($repository[$name->getIdentifier()])) {
                        $promise->success($repository[$name->getIdentifier()]);
                    } else {
                        $promise->fail(new \DomainException());
                    }

                    return $this->tasksByRunner;
                });

            $this->tasksByRunner
                ->expects(self::any())
                ->method('register')
                ->willReturnCallback(function (RunnerInterface $name, $value) use (&$repository) {
                    $repository[$name->getIdentifier()] = $value;

                    return $this->tasksByRunner;
                });

            $this->tasksByRunner
                ->expects(self::any())
                ->method('remove')
                ->willReturnCallback(function (RunnerInterface $name) use (&$repository) {
                    unset($repository[$name->getIdentifier()]);

                    return $this->tasksByRunner;
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
                ->method('get')
                ->willReturnCallback(function (TaskInterface $name, PromiseInterface $promise) use (&$repository) {
                    if (isset($repository[$name->getId()])) {
                        $promise->success($repository[$name->getId()]);
                    } else {
                        $promise->fail(new \DomainException());
                    }

                    return $this->tasksManagerByTasks;
                });

            $this->tasksManagerByTasks
                ->expects(self::any())
                ->method('register')
                ->willReturnCallback(function (TaskInterface $name, $value) use (&$repository) {
                    $repository[$name->getId()] = $value;

                    return $this->tasksManagerByTasks;
                });

            $this->tasksManagerByTasks
                ->expects(self::any())
                ->method('remove')
                ->willReturnCallback(function (TaskInterface $name) use (&$repository) {
                    unset($repository[$name->getId()]);

                    return $this->tasksManagerByTasks;
                });
        }

        return $this->tasksManagerByTasks;
    }

    /**
     * @return TasksStandbyRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getTasksStandbyRegistryMock(): TasksStandbyRegistryInterface
    {
        if (!$this->tasksStandbyRegistry instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->tasksStandbyRegistry = $this->createMock(TasksStandbyRegistryInterface::class);

            $tasksStandbyRegistry = $this->tasksStandbyRegistry;
            $queue = [];
            $this->tasksStandbyRegistry
                ->expects(self::any())
                ->method('enqueue')
                ->willReturnCallback(function (RunnerInterface $runner, TaskInterface $task) use (&$queue, $tasksStandbyRegistry) {
                    $queue[$runner->getIdentifier()][] = $task;

                    return $tasksStandbyRegistry;
                });

            $tasksStandbyRegistry = $this->tasksStandbyRegistry;
            $this->tasksStandbyRegistry
                ->expects(self::any())
                ->method('dequeue')
                ->willReturnCallback(function (RunnerInterface $runner, PromiseInterface $promise) use (&$queue, $tasksStandbyRegistry) {
                    if (empty($queue[$runner->getIdentifier()])) {
                        $promise->fail(new \OutOfBoundsException());
                    } else {
                        $promise->success(array_shift($queue[$runner->getIdentifier()]));
                    }

                    return $this->tasksStandbyRegistry;
                });
        }

        return $this->tasksStandbyRegistry;
    }

    /**
     * @return LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getLoggerMock(): LoggerInterface
    {
        if (!$this->logger instanceof LoggerInterface) {
            $this->logger = $this->createMock(LoggerInterface::class);
        }

        return $this->logger;
    }

    /**
     * @return RunnerManagerInterface|RunnerManager
     */
    public function buildManager(): RunnerManagerInterface
    {
        return new RunnerManager(
            $this->getTasksByRunnerMock(),
            $this->getTasksManagerByTasksMock(),
            $this->getTasksStandbyRegistryMock(),
            $this->getLoggerMock()
        );
    }

    public function testRegisterMeMustRememberToRunnerItsCurrentlyTask()
    {
        $task = $this->createMock(TaskInterface::class);

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::once())->method('rememberYourCurrentTask')->with($task)->willReturnSelf();
        $runner->expects(self::any())->method('getIdentifier')->willReturn('abc');

        $this->getTasksByRunnerMock()->register($runner, $task);

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $this->buildManager()->registerMe($runner)
        );
    }

    public function testManagerCanYouExecuteOnBusyRunnerMustNotCallExecute()
    {
        $task = $this->createMock(TaskInterface::class);

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::once())->method('rememberYourCurrentTask')->with($task)->willReturnSelf();
        $runner->expects(self::never())->method('execute');
        $runner->expects(self::any())->method('getIdentifier')->willReturn('abc');

        $this->getTasksByRunnerMock()->register($runner, $task);

        $runner->expects(self::any())
            ->method('canYouExecute')
            ->willReturnCallback(function (RunnerManagerInterface $manager, TaskInterface $task) use ($runner) {
                $manager->taskAccepted($runner, $task);

                return $runner;
            });

        $this->getTasksStandbyRegistryMock()
            ->expects(self::once())
            ->method('enqueue')
            ->willReturnSelf();

        $manager = $this->buildManager();
        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->registerMe($runner)
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->executeForMeThisTask(
                $this->createMock(TaskManagerInterface::class),
                $task
            )
        );
    }

    public function testManagerCanYouExecuteOnBusyRunnerMustNotCallExecuteCanCallLoadNextTaskForAfter()
    {
        $task = $this->createMock(TaskInterface::class);

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::once())->method('rememberYourCurrentTask')->with($task)->willReturnSelf();
        $runner->expects(self::once())->method('execute');
        $runner->expects(self::any())->method('getIdentifier')->willReturn('abc');

        $runner->expects(self::any())
            ->method('canYouExecute')
            ->willReturnCallback(function (RunnerManagerInterface $manager, TaskInterface $task) use ($runner) {
                $manager->taskAccepted($runner, $task);

                return $runner;
            });

        $this->getTasksByRunnerMock()->register($runner, $task);

        $this->getTasksStandbyRegistryMock()
            ->expects(self::once())
            ->method('enqueue')
            ->willReturnSelf();

        $manager = $this->buildManager();
        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->registerMe($runner)
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->executeForMeThisTask(
                $this->createMock(TaskManagerInterface::class),
                $task
            )
        );

        $this->getTasksByRunnerMock()->remove($runner);

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->loadNextTaskFor($runner)
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testManagerLoadNextTaskForAfterEnqueueOnException()
    {
        $task = $this->createMock(TaskInterface::class);

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::once())->method('rememberYourCurrentTask')->with($task)->willReturnSelf();
        $runner->expects(self::once())->method('execute')->willThrowException(new \Exception());
        $runner->expects(self::any())->method('getIdentifier')->willReturn('abc');

        $this->getTasksByRunnerMock()->register($runner, $task);

        $runner->expects(self::any())
            ->method('canYouExecute')
            ->willReturnCallback(function (RunnerManagerInterface $manager, TaskInterface $task) use ($runner) {
                $manager->taskAccepted($runner, $task);

                return $runner;
            });

        $this->getTasksStandbyRegistryMock()
            ->expects(self::exactly(2))
            ->method('enqueue')
            ->willReturnSelf();

        $this->getLoggerMock()
            ->expects(self::once())
            ->method('critical');

        $manager = $this->buildManager();
        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->registerMe($runner)
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->executeForMeThisTask(
                $this->createMock(TaskManagerInterface::class),
                $task
            )
        );

        $this->getTasksByRunnerMock()->remove($runner);

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->loadNextTaskFor($runner)
        );
    }

    public function testBashMethodsLoadNextTasks()
    {
        $task = $this->createMock(TaskInterface::class);

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::once())->method('execute');
        $runner->expects(self::any())->method('getIdentifier')->willReturn('abc');

        $this->getTasksStandbyRegistryMock()->enqueue($runner, $task);

        $manager = $this->buildManager();
        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->registerMe($runner)
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->loadNextTasks()
        );
    }

    public function testPushStatusReturnWithRunnerMonoTask()
    {
        $this->getTasksByRunnerMock()
            ->expects(self::never())
            ->method('remove');

        parent::testPushStatusReturn();
    }

    public function testPushStatusReturnFinalWithRunnerMonoTask()
    {
        $manager = $this->buildManager();

        $this->getTasksByRunnerMock()
            ->expects(self::once())
            ->method('remove');

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::any())->method('getIdentifier')->willReturn('runner');
        $runner->expects(self::any())->method('supportsMultiplesTasks')->willReturn(false);
        $status = $this->createMock(StatusInterface::class);
        $status->expects(self::any())->method('isFinal')->willReturn(true);
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

    public function testPushStatusReturnNotFinalWithRunnerMultiTask()
    {
        $manager = $this->buildManager();

        $this->getTasksByRunnerMock()
            ->expects(self::once())
            ->method('remove');

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::any())->method('getIdentifier')->willReturn('runner');
        $runner->expects(self::any())->method('supportsMultiplesTasks')->willReturn(true);
        $status = $this->createMock(StatusInterface::class);
        $status->expects(self::any())->method('isFinal')->willReturn(false);
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

    public function testPushStatusReturnFinalWithRunnerMultiTask()
    {
        $manager = $this->buildManager();

        $this->getTasksByRunnerMock()
            ->expects(self::once())
            ->method('remove');

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::any())->method('getIdentifier')->willReturn('runner');
        $runner->expects(self::any())->method('supportsMultiplesTasks')->willReturn(true);
        $status = $this->createMock(StatusInterface::class);
        $status->expects(self::any())->method('isFinal')->willReturn(true);
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

    public function testPushStatusReturnAnotherNotFinalWithRunnerMultiTask()
    {
        $manager = $this->buildManager();

        $this->getTasksByRunnerMock()
            ->expects(self::never())
            ->method('remove');

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::any())->method('getIdentifier')->willReturn('runner');
        $runner->expects(self::any())->method('supportsMultiplesTasks')->willReturn(true);
        $status = $this->createMock(StatusInterface::class);
        $status->expects(self::any())->method('isFinal')->willReturn(false);
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

        $newTask = $this->createMock(TaskInterface::class);
        $newTask->expects(self::any())->method('getUrl')->willReturn('http://foo.bar');

        $anotherTask = $this->createMock(TaskInterface::class);
        $anotherTask->expects(self::any())->method('getUrl')->willReturn('http://foo2.bar');

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
                $newTask
            )
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->pushStatus(
                $runner,
                $anotherTask,
                $status
            )
        );
    }

    public function testPushStatusReturnAnotherFinalWithRunnerMultiTask()
    {
        $manager = $this->buildManager();

        $this->getTasksByRunnerMock()
            ->expects(self::never())
            ->method('remove');

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::any())->method('getIdentifier')->willReturn('runner');
        $runner->expects(self::any())->method('supportsMultiplesTasks')->willReturn(true);
        $status = $this->createMock(StatusInterface::class);
        $status->expects(self::any())->method('isFinal')->willReturn(true);
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

        $newTask = $this->createMock(TaskInterface::class);
        $newTask->expects(self::any())->method('getUrl')->willReturn('http://foo.bar');

        $anotherTask = $this->createMock(TaskInterface::class);
        $anotherTask->expects(self::any())->method('getUrl')->willReturn('http://foo2.bar');

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
                $newTask
            )
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->pushStatus(
                $runner,
                $anotherTask,
                $status
            )
        );
    }
}
