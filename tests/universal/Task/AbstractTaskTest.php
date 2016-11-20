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

namespace Teknoo\Tests\East\CodeRunner\Task;

use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;

abstract class AbstractTaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * To get an instance of the class to test.
     *
     * @return TaskInterface
     */
    abstract public function buildTask(): TaskInterface;

    /**
     * @expectedException \Throwable
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
     * @expectedException \UnexpectedValueException
     */
    public function testGetCodeExceptionOnCodeMissing()
    {
        $this->buildTask()->getCode();
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testGetUrlExceptionOnUrlMissing()
    {
        $this->buildTask()->getUrl();
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testGetStatusExceptionOnStatusMissing()
    {
        $this->buildTask()->getStatus();
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testGetResultExceptionOnResultMissing()
    {
        $this->buildTask()->getResult();
    }

    /**
     * @expectedException \Throwable
     */
    public function testRegisterUrlExceptionOnBadUrl()
    {
        $this->buildTask()->registerUrl(
            new \stdClass()
        );
    }

    public function testRegisterUrlBehavior()
    {
        $task = $this->buildTask();
        self::assertInstanceOf(
            TaskInterface::class,
            $task->registerUrl(
                '/hello/world'
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
     * @expectedException \Throwable
     */
    public function testRegisterResultExceptionOnBadManager()
    {
        $this->buildTask()->registerResult(
            new \stdClass(),
            $this->createMock(ResultInterface::class)
        );
    }

    /**
     * @expectedException \Throwable
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
        $task->setCode($this->createMock(CodeInterface::class));
        $task->registerStatus($this->createMock(StatusInterface::class));
        $task->registerUrl('https://teknoo.software/foo/bar');

        $result = $this->createMock(ResultInterface::class);
        self::assertInstanceOf(
            TaskInterface::class,
            $task->registerResult(
                $this->createMock(TaskManagerInterface::class),
                $result
            )
        );

        self::assertInstanceOf(
            ResultInterface::class,
            $task->getResult()
        );

        self::assertEquals(
            $result,
            $task->getResult()
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testRegisterStatusExceptionOnBadStatus()
    {
        $this->buildTask()->registerStatus(
            new \stdClass()
        );
    }

    public function testRegisterStatusBehavior()
    {
        $task = $this->buildTask();
        $task->setCode($this->createMock(CodeInterface::class));
        $task->registerStatus($this->createMock(StatusInterface::class));
        $task->registerUrl('https://teknoo.software/foo/bar');

        $status = $this->createMock(StatusInterface::class);
        self::assertInstanceOf(
            TaskInterface::class,
            $task->registerStatus(
                $status
            )
        );

        self::assertInstanceOf(
            StatusInterface::class,
            $task->getStatus()
        );

        self::assertEquals(
            $status,
            $task->getStatus()
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testJsonDeserializeBadClass()
    {
        $task = $this->buildTask();
        $className = get_class($task);
        $className::jsonDeserialize(['class' => '\DateTime']);
    }

    public function testJsonEncodeDecode()
    {
        $task = $this->buildTask();
        $className = get_class($task);
        self::assertEquals(
            $task,
            $className::jsonDeserialize(json_decode(json_encode($task), true))
        );
    }
}
