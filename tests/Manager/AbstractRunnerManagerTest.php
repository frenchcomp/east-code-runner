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

use Teknoo\East\CodeRunnerBundle\Manager\RunnerManagerInterface;
use Teknoo\East\CodeRunnerBundle\Manager\TaskManagerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Task\ResultInterface;
use Teknoo\East\CodeRunnerBundle\Task\TaskInterface;

abstract class AbstractRunnerManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * To get an instance of the class to test
     * @return RunnerManagerInterface
     */
    abstract public function buildManager(): RunnerManagerInterface;

    /**
     * @exceptedException \Throwable
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
     * @exceptedException \Throwable
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
     * @exceptedException \Throwable
     */
    public function testPushResultBadRunner()
    {
        $this->buildManager()->pushResult(
            new \stdClass(),
            $this->createMock(ResultInterface::class)
        );
    }

    /**
     * @exceptedException \Throwable
     */
    public function testPushResultBadResult()
    {
        $this->buildManager()->pushResult(
            $this->createMock(RunnerInterface::class),
            new \stdClass()
        );
    }

    public function testPushResultReturn()
    {
        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $this->buildManager()->pushResult(
                $this->createMock(RunnerInterface::class),
                $this->createMock(ResultInterface::class)
            )
        );
    }

    /**
     * @exceptedException \DomainException
     */
    abstract public function testPushResultExceptionTaskUnknown();

    /**
     * @exceptedException \Throwable
     */
    public function testExecuteForMeThisTaskBadManager()
    {
        $this->buildManager()->executeForMeThisTask(
            new \stdClass(),
            $this->createMock(TaskInterface::class)
        );
    }

    /**
     * @exceptedException \Throwable
     */
    public function testExecuteForMeThisTaskBadTask()
    {
        $this->buildManager()->executeForMeThisTask(
            $this->createMock(TaskManagerInterface::class),
            new \stdClass()
        );
    }

    public function testExecuteForMeThisTaskReturn()
    {
        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $this->buildManager()->executeForMeThisTask(
                $this->createMock(TaskManagerInterface::class),
                $this->createMock(TaskInterface::class)
            )
        );
    }

    /**
     * @exceptedException \DomainException
     */
    abstract public function testExecuteForMeThisTaskExceptionWhenTaskNotExecutableByAnyRunners();
}