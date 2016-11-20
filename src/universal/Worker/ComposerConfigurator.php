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

namespace Teknoo\East\CodeRunner\Worker;

use AdamBrett\ShellWrapper\Command;
use AdamBrett\ShellWrapper\Command\Param;
use AdamBrett\ShellWrapper\Runners\Runner;
use Gaufrette\Filesystem;
use Teknoo\East\CodeRunner\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunner\Worker\Interfaces\ComposerConfiguratorInterface;
use Teknoo\East\CodeRunner\Worker\Interfaces\RunnerInterface;

class ComposerConfigurator implements ComposerConfiguratorInterface
{
    const COMPOSER_JSON_FILE = 'composer.json';

    /**
     * @var Runner
     */
    private $commandRunner;

    /**
     * @var Command
     */
    private $composerCommand;

    /**
     * @var string
     */
    private $composerInstruction;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var string
     */
    private $composerDirectoryParam;

    /**
     * ComposerConfigurator constructor.
     *
     * @param Runner     $commandRunner
     * @param Command    $composerCommand
     * @param string     $composerInstruction
     * @param Filesystem $fileSystem
     * @param string     $composerDirectoryParam
     */
    public function __construct(
        Runner $commandRunner,
        Command $composerCommand,
        string $composerInstruction,
        Filesystem $fileSystem,
        string $composerDirectoryParam
    ) {
        $this->commandRunner = $commandRunner;
        $this->composerCommand = $composerCommand;
        $this->composerInstruction = $composerInstruction;
        $this->fileSystem = $fileSystem;
        $this->composerDirectoryParam = $composerDirectoryParam;
    }

    /**
     * @return ComposerConfiguratorInterface
     */
    public function reset(): ComposerConfiguratorInterface
    {
        $this->fileSystem->delete(self::COMPOSER_JSON_FILE);

        return $this;
    }

    /**
     * @param CodeInterface $code
     *
     * @return string
     */
    private function convertToRequirePackage(CodeInterface $code): string
    {
        return \json_encode(['require' => $code->getNeededPackages()]);
    }

    /**
     * @param CodeInterface $code
     */
    private function generateComposerFile(CodeInterface $code)
    {
        $this->fileSystem->write(self::COMPOSER_JSON_FILE, $this->convertToRequirePackage($code));
    }

    private function runComposer()
    {
        $composerCommand = clone $this->composerCommand;
        $composerCommand->addParam(new Param($this->composerInstruction));
        $composerCommand->addParam(new Param('--no-interaction'));
        $composerCommand->addParam(new Param($this->composerDirectoryParam));

        $this->commandRunner->run($composerCommand);
    }

    /**
     * @param CodeInterface   $code
     * @param RunnerInterface $runner
     *
     * @return ComposerConfiguratorInterface
     */
    public function configure(CodeInterface $code, RunnerInterface $runner): ComposerConfiguratorInterface
    {
        $this->generateComposerFile($code);

        $this->runComposer();

        $runner->composerIsReady($code);

        return $this;
    }
}
