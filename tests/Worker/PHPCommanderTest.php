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
namespace Teknoo\Tests\East\CodeRunnerBundle\Worker;

use AdamBrett\ShellWrapper\Command;
use AdamBrett\ShellWrapper\Runners\Exec;
use AdamBrett\ShellWrapper\Runners\ReturnValue;
use AdamBrett\ShellWrapper\Runners\Runner;
use Gaufrette\Filesystem;
use Teknoo\East\CodeRunnerBundle\Worker\Interfaces\PHPCommanderInterface;
use Teknoo\East\CodeRunnerBundle\Worker\PHPCommander;

/**
 * @covers \Teknoo\East\CodeRunnerBundle\Worker\PHPCommander
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

}