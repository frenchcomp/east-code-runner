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

use Teknoo\East\CodeRunnerBundle\Manager\TaskManagerInterface;
use Teknoo\East\CodeRunnerBundle\Task\TaskInterface;

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
    abstract public function testUpdateMyExecutionStatusExceptionWithUnknownTask();

    public function testUpdateMyExecutionStatusReturn()
    {
        self::assertInstanceOf(
            TaskManagerInterface::class,
            $this->buildManager()->updateMyExecutionStatus($this->createMock(TaskInterface::class))
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
    abstract public function testSetMyExecutionResultExceptionWithUnknownTask();

    public function testSetMyExecutionResultReturn()
    {
        self::assertInstanceOf(
            TaskManagerInterface::class,
            $this->buildManager()->setMyExecutionResult($this->createMock(TaskInterface::class))
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