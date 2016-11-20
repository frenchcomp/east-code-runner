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

namespace Teknoo\Tests\East\CodeRunner\Worker;

use Teknoo\East\CodeRunner\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunner\Worker\Interfaces\PHPCommanderInterface;
use Teknoo\East\CodeRunner\Worker\Interfaces\RunnerInterface;

abstract class AbstractPHPCommanderTest extends \PHPUnit_Framework_TestCase
{
    abstract public function buildCommander(): PHPCommanderInterface;

    public function testResetReturn()
    {
        self::assertInstanceOf(
            PHPCommanderInterface::class,
            $this->buildCommander()->reset()
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testExecuteBadCode()
    {
        $this->buildCommander()->execute(new \stdClass(), $this->createMock(RunnerInterface::class));
    }

    /**
     * @expectedException \Throwable
     */
    public function testExecuteBadRunner()
    {
        $this->buildCommander()->execute($this->createMock(CodeInterface::class), new \stdClass());
    }

    public function testExecute()
    {
        $code = $this->createMock(CodeInterface::class);
        $code->expects(self::any())->method('getCode')->willReturn('echo "Hello World";');

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::once())->method('codeExecuted')->willReturnSelf();

        self::assertInstanceOf(
            PHPCommanderInterface::class,
            $this->buildCommander()->execute(
                $code,
                $runner
            )
        );
    }
}
