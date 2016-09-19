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
namespace Teknoo\Tests\East\CodeRunnerBundle\Runner;

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\Capability;
use Teknoo\East\CodeRunnerBundle\Runner\ClassicPHP7Runner\States\Awaiting;
use Teknoo\East\CodeRunnerBundle\Runner\ClassicPHP7Runner\States\Busy;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\ClassicPHP7Runner\ClassicPHP7Runner;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\East\CodeRunnerBundle\Task\PHPCode;
use Teknoo\East\CodeRunnerBundle\Task\TextResult;

/**
 * Class ClassicPHP7RunnerTest
 * @covers \Teknoo\East\CodeRunnerBundle\Runner\ClassicPHP7Runner\ClassicPHP7Runner
 * @covers \Teknoo\East\CodeRunnerBundle\Runner\ClassicPHP7Runner\States\Awaiting
 * @covers \Teknoo\East\CodeRunnerBundle\Runner\ClassicPHP7Runner\states\BUsy
 */
class ClassicPHP7RunnerTest extends AbstractRunnerTest
{
    public function buildRunner(): RunnerInterface
    {
        $runner = new ClassicPHP7Runner('ClassicPHP7Runner1', 'ClassicPHP7Runner', 'PHP7.0', [new Capability('package', 'eval')]);
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
            ->willReturnSelf();

        $manager->expects(self::never())
            ->method('taskRejected');

        $manager->expects($this->once())->method('pushResult')->willReturnCallback(
            function (RunnerInterface $runner, TextResult $textResult) use ($manager) {
                self::assertEquals(45, $textResult->getOutput());
                self::assertEquals('', $textResult->getErrors());
                self::assertEquals('PHP7.0', $textResult->getVersion());
                self::assertGreaterThanOrEqual(\memory_get_usage(true), $textResult->getMemorySize());
                self::assertGreaterThanOrEqual(5000, $textResult->getTimeExecution());

                return $manager;
            }
        );

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

        self::assertInstanceOf(
            RunnerInterface::class,
            $runner->canYouExecute(
                $manager,
                $task
            )
        );

        self::assertTrue($runner->inState(Awaiting::class));
    }

    public function testCanYouExecuteCodeNotRunnableByThisRunnerMissingPackage()
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
            ->willReturnSelf();

        $manager->expects(self::never())
            ->method('taskRejected');

        $manager->expects($this->once())->method('pushResult')->willReturnCallback(
            function (RunnerInterface $runner, TextResult $textResult) use ($manager) {
                self::assertEquals('', $textResult->getOutput());
                self::assertEquals('Division by zero', $textResult->getErrors());
                self::assertEquals('PHP7.0', $textResult->getVersion());
                self::assertGreaterThanOrEqual(\memory_get_usage(true), $textResult->getMemorySize());
                self::assertGreaterThanOrEqual(0, $textResult->getTimeExecution());

                return $manager;
            }
        );

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
}