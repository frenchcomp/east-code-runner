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
namespace Teknoo\Tests\East\CodeRunnerBundle\Task;

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;

abstract class AbstractTaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * To get an instance of the class to test
     * @return TaskInterface
     */
    abstract public function buildTask(): TaskInterface;

    /**
     * @exceptedException \Throwable
     */
    public function testSetCodeExceptionOnBadInput()
    {
        $this->buildTask()->setCode(new \stdClass());
    }

    public function testGetSetCodeBehavior()
    {
        $code = $this->createMock(CodeInterface::class);
        $task = $this->buildTask();

        self::assertInstanceOf(
            TaskInterface::class,
            $task->setCode($code)
        );

        self::assertInstanceOf(
            CodeInterface::class,
            $task->getCode()
        );

        self::assertEquals(
            $code,
            $task->getCode()
        );
    }

    /**
     * @exceptedException \UnexpectedValueException
     */
    public function testGetCodeExceptionOnCodeMissing()
    {
        $this->buildTask()->getCode();
    }

    /**
     * @exceptedException \UnexpectedValueException
     */
    public function testGetUrlExceptionOnUrlMissing()
    {
        $this->buildTask()->getUrl();
    }

    /**
     * @exceptedException \UnexpectedValueException
     */
    public function testGetStatusExceptionOnStatusMissing()
    {
        $this->buildTask()->getStatus();
    }

    /**
     * @exceptedException \UnexpectedValueException
     */
    public function testGetResultExceptionOnResultMissing()
    {
        $this->buildTask()->getResult();
    }

    /**
     * @exceptedException \Throwable
     */
    public function testRegisterTaskManagerExecutingExceptionOnBadUrl()
    {
        $this->buildTask()->registerTaskManagerExecuting(
            new \stdClass(),
            $this->createMock(TaskManagerInterface::class)
        );
    }

    /**
     * @exceptedException \Throwable
     */
    public function testRegisterTaskManagerExecutingExceptionOnBadManager()
    {
        $this->buildTask()->registerTaskManagerExecuting(
            '/hello/world',
            new \stdClass()
        );
    }

    public function testRegisterTaskManagerBehavior()
    {
        $task = $this->buildTask();
        self::assertInstanceOf(
            TaskInterface::class,
            $task->registerTaskManagerExecuting(
                '/hello/world',
                $this->createMock(TaskManagerInterface::class)
            )
        );

        self::assertInternalType(
            'string',
            $task->getUrl()
        );

        self::assertEquals(
            '/hello/world',
            $task->getUrl()
        );
    }

    /**
     * @exceptedException \Throwable
     */
    public function testRegisterResultExceptionOnBadManager()
    {
        $this->buildTask()->registerResult(
            new \stdClass(),
            $this->createMock(ResultInterface::class)
        );
    }

    /**
     * @exceptedException \Throwable
     */
    public function testRegisterResultExceptionOnBadResult()
    {
        $this->buildTask()->registerResult(
            $this->createMock(TaskManagerInterface::class),
            new \stdClass()
        );
    }

    public function testRegisterResultBehavior()
    {
        $task = $this->buildTask();
        $result = $this->createMock(ResultInterface::class);
        self::assertInstanceOf(
            TaskInterface::class,
            $task->registerResult(
                $this->createMock(TaskManagerInterface::class),
                $result
            )
        );

        self::assertInternalType(
            ResultInterface::class,
            $task->getResult()
        );

        self::assertEquals(
            $result,
            $task->getResult()
        );
    }
}