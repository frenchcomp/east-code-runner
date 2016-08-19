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

use Teknoo\East\CodeRunnerBundle\Task\ResultInterface;

abstract class AbstractResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * To get an instance of the class to test
     * @return ResultInterface
     */
    abstract public function buildResult(): ResultInterface;

    public function testGetOutputReturn()
    {
        self::assertInternalType(
            'string',
            $this->buildResult()->getOutput()
        );
    }

    public function testGetErrorsReturn()
    {
        self::assertInternalType(
            'string',
            $this->buildResult()->getErrors()
        );
    }

    public function testGetVersionReturn()
    {
        self::assertInternalType(
            'string',
            $this->buildResult()->getVersion()
        );
    }

    public function testGetMemorySizeReturn()
    {
        self::assertInternalType(
            'string',
            $this->buildResult()->getMemorySize()
        );
    }

    public function testGetTimeExecutionReturn()
    {
        self::assertInternalType(
            'string',
            $this->buildResult()->getTimeExecution()
        );
    }

    /**
     * @expectedException \Teknoo\Immutable\Exception\ImmutableException
     */
    public function testValueObjectBehaviorSetException()
    {
        $this->buildResult()->foo = 'bar';
    }

    /**
     * @expectedException \Teknoo\Immutable\Exception\ImmutableException
     */
    public function testValueObjectBehaviorUnsetException()
    {
        unset($this->buildResult()->foo);
    }

    /**
     * @expectedException \Teknoo\Immutable\Exception\ImmutableException
     */
    public function testValueObjectBehaviorConstructor()
    {
        $this->buildResult()->__construct();
    }

}