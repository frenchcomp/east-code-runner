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

use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;

/**
 * Class AbstractRunnerTest
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractRunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * To get an instance of the class to test.
     *
     * @return RunnerInterface
     */
    abstract public function buildRunner(): RunnerInterface;

    public function testGetIdentifierReturn()
    {
        self::assertInternalType(
            'string',
            $this->buildRunner()->getIdentifier()
        );
    }

    public function testGetNameReturn()
    {
        self::assertInternalType(
            'string',
            $this->buildRunner()->getName()
        );
    }

    public function testGetVersion()
    {
        self::assertInternalType(
            'string',
            $this->buildRunner()->getVersion()
        );
    }

    public function testGetCapabilitiesReturn()
    {
        self::assertInternalType(
            'array',
            $this->buildRunner()->getCapabilities()
        );
    }

    public function testResetReturn()
    {
        self::assertInstanceOf(
            RunnerInterface::class,
            $this->buildRunner()->reset()
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testCanYouExecuteExceptionOnBadManager()
    {
        $this->buildRunner()->canYouExecute(
            new \stdClass(),
            $this->createMock(TaskInterface::class)
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testCanYouExecuteExceptionOnBadResult()
    {
        $this->buildRunner()->canYouExecute(
            $this->createMock(RunnerManagerInterface::class),
            new \stdClass()
        );
    }

    public function testRegisterResultBehavior()
    {
        $runner = $this->buildRunner();
        self::assertInstanceOf(
            RunnerInterface::class,
            $runner->canYouExecute(
                $this->createMock(RunnerManagerInterface::class),
                $this->createMock(TaskInterface::class)
            )
        );
    }

    /**
     * @exceptedException \DomainException
     */
    abstract public function testCanYouExecuteCodeNotRunnableByThisRunner();

    /**
     * @exceptedException \LogicException
     */
    abstract public function testCanYouExecuteCodeInvalid();

    public function testCanYouExecuteAcceptBehaviorMustCallTaskAccepted()
    {
        $manager = $this->createMock(RunnerManagerInterface::class);
        $runner = $this->buildRunner();
        $task = $this->createMock(TaskInterface::class);

        $manager->expects(self::once())
            ->method('taskAccepted')
            ->with($runner, $task)
            ->willReturnSelf();

        $manager->expects(self::never())
            ->method('taskRejected');

        self::assertInstanceOf(
            RunnerInterface::class,
            $runner->canYouExecute(
                $manager,
                $task
            )
        );
    }

    public function testCanYouExecuteRejectBehaviorMustCallTaskRejected()
    {
        $manager = $this->createMock(RunnerManagerInterface::class);
        $runner = $this->buildRunner();
        $task = $this->createMock(TaskInterface::class);

        $manager->expects(self::once())
            ->method('taskRejected')
            ->with($runner, $task)
            ->willReturnSelf();

        $manager->expects(self::never())
            ->method('taskAccepted');

        self::assertInstanceOf(
            RunnerInterface::class,
            $runner->canYouExecute(
                $manager,
                $task
            )
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testRememberYourCurrentTaskBadTask()
    {
        $this->buildRunner()->rememberYourCurrentTask(
            new \stdClass()
        );
    }

    public function testRememberYourCurrentTask()
    {
        $runner = $this->buildRunner();
        self::assertInstanceOf(
            RunnerInterface::class,
            $runner->rememberYourCurrentTask(
                $this->createMock(TaskInterface::class)
            )
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testExecuteExceptionOnBadManager()
    {
        $this->buildRunner()->execute(
            new \stdClass(),
            $this->createMock(TaskInterface::class)
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testExecuteExceptionOnBadResult()
    {
        $this->buildRunner()->execute(
            $this->createMock(RunnerManagerInterface::class),
            new \stdClass()
        );
    }

    public function testExecuteResultBehavior()
    {
        $runner = $this->buildRunner();
        self::assertInstanceOf(
            RunnerInterface::class,
            $runner->execute(
                $this->createMock(RunnerManagerInterface::class),
                $this->createMock(TaskInterface::class)
            )
        );
    }

    /**
     * @exceptedException \DomainException
     */
    abstract public function testExecuteCodeNotRunnableByThisRunner();

    /**
     * @exceptedException \LogicException
     */
    abstract public function testExecuteCodeInvalid();
}
