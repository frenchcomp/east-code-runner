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
use AdamBrett\ShellWrapper\Runners\Runner;
use Gaufrette\Filesystem;
use Teknoo\East\CodeRunnerBundle\Worker\ComposerConfigurator;
use Teknoo\East\CodeRunnerBundle\Worker\Interfaces\ComposerConfiguratorInterface;

/**
 * @covers \Teknoo\East\CodeRunnerBundle\Worker\ComposerConfigurator
 */
class ComposerConfiguratorTest extends AbstractComposerConfiguratorTest
{
    /**
     * @var Runner
     */
    private $commandRunner;

    /**
     * @var Command
     */
    private $composerCommand;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @return Runner|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getCommandRunnerMock(): Runner
    {
        if (!$this->commandRunner instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->commandRunner = $this->createMock(Runner::class);
        }

        return $this->commandRunner;
    }

    /**
     * @return Command|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getComposerCommandMock(): Command
    {
        if (!$this->composerCommand instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->composerCommand = $this->createMock(Command::class);
        }

        return $this->composerCommand;
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

    public function buildConfigurator(): ComposerConfiguratorInterface
    {
        return new ComposerConfigurator(
            $this->getCommandRunnerMock(),
            $this->getComposerCommandMock(),
            'install',
            $this->getFileSystemMock(),
            '-d foo/bar'
        );
    }
}