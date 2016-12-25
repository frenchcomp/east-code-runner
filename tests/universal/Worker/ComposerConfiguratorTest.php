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
use AdamBrett\ShellWrapper\Runners\Runner;
use Gaufrette\Filesystem;
use Teknoo\East\CodeRunner\Worker\ComposerConfigurator;
use Teknoo\East\CodeRunner\Worker\Interfaces\ComposerConfiguratorInterface;

/**
 * @covers \Teknoo\East\CodeRunner\Worker\ComposerConfigurator
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

    public function testResetReturn()
    {
        $this->getFileSystemMock()
            ->expects(self::once())
            ->method('delete')
            ->with(ComposerConfigurator::COMPOSER_JSON_FILE);

        parent::testResetReturn();
    }

    public function testConfigure()
    {
        $this->getFileSystemMock()
            ->expects(self::once())
            ->method('write')
            ->with(ComposerConfigurator::COMPOSER_JSON_FILE, \json_encode(['require' => ['foo' => '2.3.4', 'bar' => '*']]))
            ->willReturn(123);

        $this->getCommandRunnerMock()
            ->expects(self::once())
            ->method('run');

        parent::testConfigure();
    }
}
