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

namespace Teknoo\Tests\East\CodeRunner\Worker;

use AdamBrett\ShellWrapper\Command;
use AdamBrett\ShellWrapper\Runners\Exec;
use AdamBrett\ShellWrapper\Runners\ReturnValue;
use AdamBrett\ShellWrapper\Runners\Runner;
use Gaufrette\Filesystem;
use Teknoo\East\CodeRunner\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunner\Task\TextResult;
use Teknoo\East\CodeRunner\Worker\Interfaces\PHPCommanderInterface;
use Teknoo\East\CodeRunner\Worker\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Worker\PHPCommander;

/**
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\East\CodeRunner\Worker\PHPCommander
 */
class PHPCommanderTest extends AbstractPHPCommanderTest
{
    /**
     * @var Command
     */
    private $phpCommand;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var Runner|ReturnValue
     */
    private $commandRunner;

    /**
     * @return Command|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getPhpCommandMock(): Command
    {
        if (!$this->phpCommand instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->phpCommand = $this->createMock(Command::class);
        }

        return $this->phpCommand;
    }

    /**
     * @return Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getFileSystemMock(): Filesystem
    {
        if (!$this->fileSystem instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->fileSystem = $this->createMock(Filesystem::class);
        }

        return $this->fileSystem;
    }

    /**
     * @return ReturnValue|Runner|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getCommandRunnerMock()
    {
        if (!$this->commandRunner instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->commandRunner = $this->createMock(Exec::class);
        }

        return $this->commandRunner;
    }

    public function buildCommander(): PHPCommanderInterface
    {
        return new PHPCommander(
            $this->getPhpCommandMock(),
            $this->getFileSystemMock(),
            $this->getCommandRunnerMock(),
            '7.0'
        );
    }

    public function testResetReturn()
    {
        $this->getFileSystemMock()
            ->expects(self::once())
            ->method('delete')
            ->with(PHPCommander::TEMP_FILE);

        parent::testResetReturn();
    }

    public function testExecute()
    {
        $this->getFileSystemMock()
            ->expects(self::once())
            ->method('write')
            ->with(PHPCommander::TEMP_FILE, '<?php'.PHP_EOL.'require_once ("vendor/autoload.php");'.PHP_EOL.PHP_EOL.'echo "Hello World";')
            ->willReturn(123);

        $this->getCommandRunnerMock()
            ->expects(self::once())
            ->method('run');

        $this->getCommandRunnerMock()
            ->expects(self::once())
            ->method('getReturnValue')
            ->willReturn('Hello World');

        $code = $this->createMock(CodeInterface::class);
        $code->expects(self::any())->method('getCode')->willReturn('echo "Hello World";');
        $oriCode = $code;

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::never())->method('errorInCode');

        $runner->expects(self::once())
            ->method('codeExecuted')
            ->willReturnCallback(
                function (CodeInterface $code, ResultInterface $result) use ($oriCode, $runner) {
                    self::assertEquals($oriCode, $code);
                    self::assertInstanceOf(TextResult::class, $result);
                    self::assertEquals('Hello World', $result->getOutput());
                    self::assertEquals('', $result->getErrors());

                    return $runner;
                }
            );

        self::assertInstanceOf(
            PHPCommanderInterface::class,
            $this->buildCommander()->execute(
                $code,
                $runner
            )
        );
    }

    public function testExecuteError()
    {
        $this->getFileSystemMock()
            ->expects(self::once())
            ->method('write')
            ->with(PHPCommander::TEMP_FILE, '<?php'.PHP_EOL.'require_once ("vendor/autoload.php");'.PHP_EOL.PHP_EOL.'echo "Hello World";')
            ->willReturn(123);

        $this->getCommandRunnerMock()
            ->expects(self::once())
            ->method('run');

        $this->getCommandRunnerMock()
            ->expects(self::once())
            ->method('getReturnValue')
            ->willThrowException(new \Exception('fooBar'));

        $code = $this->createMock(CodeInterface::class);
        $code->expects(self::any())->method('getCode')->willReturn('echo "Hello World";');
        $oriCode = $code;

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::never())->method('codeExecuted');

        $runner->expects(self::once())
            ->method('errorInCode')
            ->willReturnCallback(
                function (CodeInterface $code, ResultInterface $result) use ($oriCode, $runner) {
                    self::assertEquals($oriCode, $code);
                    self::assertInstanceOf(TextResult::class, $result);
                    self::assertEquals('', $result->getOutput());
                    $errors = explode(PHP_EOL, $result->getErrors());
                    self::assertEquals('fooBar', $errors[0]);

                    return $runner;
                }
            );

        self::assertInstanceOf(
            PHPCommanderInterface::class,
            $this->buildCommander()->execute(
                $code,
                $runner
            )
        );
    }
}
