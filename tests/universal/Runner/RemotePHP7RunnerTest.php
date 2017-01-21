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

namespace Teknoo\Tests\East\CodeRunner\Runner;

use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Runner\Capability;
use Teknoo\East\CodeRunner\Runner\RemotePHP7Runner\States\Awaiting;
use Teknoo\East\CodeRunner\Runner\RemotePHP7Runner\States\Busy;
use Teknoo\East\CodeRunner\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Runner\RemotePHP7Runner\RemotePHP7Runner;
use Teknoo\East\CodeRunner\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\CodeRunner\Task\PHPCode;

/**
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\East\CodeRunner\Runner\RemotePHP7Runner\RemotePHP7Runner
 * @covers \Teknoo\East\CodeRunner\Runner\RemotePHP7Runner\States\Awaiting
 * @covers \Teknoo\East\CodeRunner\Runner\RemotePHP7Runner\states\BUsy
 * @covers \Teknoo\East\CodeRunner\Runner\CheckRequirementsTrait
 */
class RemotePHP7RunnerTest extends AbstractRunnerTest
{
    /**
     * @var Producer
     */
    private $taskProducer;

    /**
     * @return Producer|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getProducer()
    {
        if (!$this->taskProducer instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->taskProducer = $this->createMock(Producer::class);
        }

        return $this->taskProducer;
    }

    public function buildRunner(): RunnerInterface
    {
        $runner = new RemotePHP7Runner($this->getProducer(), 'RemotePHP7Runner1', 'RemotePHP7Runner', 'PHP7.0', [new Capability('package', 'eval')]);
        $runner->addCapability(new Capability('package', 'php7'));

        return $runner;
    }

    public function testCanYouExecuteAcceptBehaviorMustCallTaskAccepted()
    {
        $manager = $this->createMock(RunnerManagerInterface::class);
        $runner = $this->buildRunner();

        $phpCode = new PHPCode('sleep(5);return 4.5*10;', [new Capability('package', 'php7')]);

        $task = $this->createMock(TaskInterface::class);
        $task->expects(self::any())->method('getCode')->willReturn($phpCode);

        $manager->expects(self::once())
            ->method('taskAccepted')
            ->with($runner, $task)
            ->willReturnCallback(function (RunnerInterface $runner, TaskInterface $task) use ($manager) {
                $runner->execute($manager, $task);

                return $manager;
            });

        $manager->expects(self::never())
            ->method('taskRejected');

        $this->getProducer()
            ->expects(self::once())
            ->method('publish')
            ->with(json_encode($task));

        self::assertInstanceOf(
            RunnerInterface::class,
            $runner->canYouExecute(
                $manager,
                $task
            )
        );

        self::assertTrue($runner->inState(Busy::class));

        $runner->reset();
        self::assertTrue($runner->inState(Awaiting::class));
    }

    public function testCanYouExecuteCodeNotRunnableByThisRunner()
    {
        $manager = $this->createMock(RunnerManagerInterface::class);
        $runner = $this->buildRunner();

        $phpCode = $this->createMock(CodeInterface::class);
        $task = $this->createMock(TaskInterface::class);
        $task->expects(self::any())->method('getCode')->willReturn($phpCode);

        $manager->expects(self::never())
            ->method('taskAccepted');

        $manager->expects(self::once())
            ->method('taskRejected')
            ->with($runner, $task)
            ->willReturnSelf();

        $this->getProducer()
            ->expects(self::never())
            ->method('publish');

        self::assertInstanceOf(
            RunnerInterface::class,
            $runner->canYouExecute(
                $manager,
                $task
            )
        );

        self::assertTrue($runner->inState(Awaiting::class));
    }

    public function testCanYouExecuteCodeNotRunnableByThisRunnerMissingPackageAndExecute()
    {
        $manager = $this->createMock(RunnerManagerInterface::class);
        $runner = $this->buildRunner();

        $phpCode = new PHPCode('return 123;', [new Capability('package', 'php8')]);
        $task = $this->createMock(TaskInterface::class);
        $task->expects(self::any())->method('getCode')->willReturn($phpCode);

        $manager->expects(self::never())
            ->method('taskAccepted');

        $manager->expects(self::once())
            ->method('taskRejected')
            ->with($runner, $task)
            ->willReturnSelf();

        $this->getProducer()
            ->expects(self::never())
            ->method('publish');

        self::assertInstanceOf(
            RunnerInterface::class,
            $runner->canYouExecute(
                $manager,
                $task
            )
        );

        self::assertTrue($runner->inState(Awaiting::class));
    }

    public function testCanYouExecuteCodeInvalid()
    {
        $manager = $this->createMock(RunnerManagerInterface::class);
        $runner = $this->buildRunner();

        $phpCode = new PHPCode('return 4/0;', [new Capability('package', 'php7')]);

        $task = $this->createMock(TaskInterface::class);
        $task->expects(self::any())->method('getCode')->willReturn($phpCode);

        $manager->expects(self::once())
            ->method('taskAccepted')
            ->with($runner, $task)
            ->willReturnCallback(function (RunnerInterface $runner, TaskInterface $task) use ($manager) {
                $runner->execute($manager, $task);

                return $manager;
            });

        $manager->expects(self::never())
            ->method('taskRejected');

        $this->getProducer()
            ->expects(self::once())
            ->method('publish')
            ->with(json_encode($task));

        self::assertInstanceOf(
            RunnerInterface::class,
            $runner->canYouExecute(
                $manager,
                $task
            )
        );

        self::assertTrue($runner->inState(Busy::class));

        $runner->reset();
        self::assertTrue($runner->inState(Awaiting::class));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExecuteCodeNotRunnableByThisRunner()
    {
        $manager = $this->createMock(RunnerManagerInterface::class);
        $runner = $this->buildRunner();
        $runner->rememberYourCurrentTask($this->createMock(TaskInterface::class));

        $phpCode = new PHPCode('return 123;', [new Capability('package', 'php8')]);
        $task = $this->createMock(TaskInterface::class);
        $task->expects(self::any())->method('getCode')->willReturn($phpCode);

        $this->getProducer()
            ->expects(self::never())
            ->method('publish');

        self::assertInstanceOf(
            RunnerInterface::class,
            $runner->execute(
                $manager,
                $task
            )
        );
    }

    public function testExecuteCodeInvalid()
    {
        $manager = $this->createMock(RunnerManagerInterface::class);
        $runner = $this->buildRunner();

        $phpCode = new PHPCode('return 4/0;', [new Capability('package', 'php7')]);

        $task = $this->createMock(TaskInterface::class);
        $task->expects(self::any())->method('getCode')->willReturn($phpCode);

        $this->getProducer()
            ->expects(self::once())
            ->method('publish')
            ->with(json_encode($task));

        self::assertInstanceOf(
            RunnerInterface::class,
            $runner->execute(
                $manager,
                $task
            )
        );
    }
}
