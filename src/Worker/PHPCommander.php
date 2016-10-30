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
namespace Teknoo\East\CodeRunnerBundle\Worker;

use AdamBrett\ShellWrapper\Command;
use AdamBrett\ShellWrapper\Command\Param;
use AdamBrett\ShellWrapper\Runners\ReturnValue;
use AdamBrett\ShellWrapper\Runners\Runner;
use Gaufrette\Filesystem;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunnerBundle\Task\TextResult;
use Teknoo\East\CodeRunnerBundle\Worker\Interfaces\PHPCommanderInterface;
use Teknoo\East\CodeRunnerBundle\Worker\Interfaces\RunnerInterface;

class PHPCommander implements PHPCommanderInterface
{
    const TEMP_FILE = 'my_app.php';

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
     * @var string
     */
    private $version;

    /**
     * @var int
     */
    private $startupTime;

    /**
     * @var int
     */
    private $executionTime;

    /**
     * PHPCommander constructor.
     * @param Command $phpCommand
     * @param Filesystem $fileSystem
     * @param ReturnValue|Runner $commandRunner
     * @param string $version
     */
    public function __construct(
        Command $phpCommand,
        Filesystem $fileSystem,
        Runner $commandRunner,
        string $version
    ) {
        $this->phpCommand = $phpCommand;
        $this->fileSystem = $fileSystem;
        $this->commandRunner = $commandRunner;
        $this->version = $version;
    }


    /**
     * @return PHPCommanderInterface
     */
    public function reset(): PHPCommanderInterface
    {
        $this->fileSystem->delete(self::TEMP_FILE);

        return $this;
    }

    /**
     * @param CodeInterface $code
     * @return string
     */
    private function generatePHPScript(CodeInterface $code): string
    {
        $phpScript = '<?php'.PHP_EOL;
        $phpScript .= 'require_once ("vendor/autoload.php");'.PHP_EOL.PHP_EOL;
        $phpScript .= $code->getCode();

        return $phpScript;
    }

    /**
     * @param CodeInterface $code
     */
    private function writePHPScript(CodeInterface $code)
    {
        $this->fileSystem->write(self::TEMP_FILE, $this->generatePHPScript($code));
    }

    /**
     *
     */
    private function executePhpScript()
    {
        $phpCommand = clone $this->phpCommand;
        $phpCommand->addParam(new Param('-f '.self::TEMP_FILE));

        $this->startupTime = \microtime(true) * 1000;

        $this->commandRunner->run($phpCommand);

        $timeAfter = \microtime(true) * 1000;
        $this->executionTime = $timeAfter - $this->startupTime;
    }

    /**
     * @return ResultInterface
     */
    private function generateResult(): ResultInterface
    {
        return new TextResult(
            (string) $this->commandRunner->getReturnValue(),
            '',
            $this->version,
            \memory_get_usage(true),
            $this->executionTime
        );
    }

    /**
     * @param CodeInterface $code
     * @param RunnerInterface $runner
     * @return PHPCommanderInterface
     */
    public function execute(CodeInterface $code, RunnerInterface $runner): PHPCommanderInterface
    {
        try {
            $this->writePHPScript($code);

            $this->executePhpScript();

            $runner->codeExecuted($code, $this->generateResult());
        } catch (\Throwable $e) {
            $timeAfter = \microtime(true) * 1000;
            $this->executionTime = $timeAfter - $this->startupTime;

            $error = $e->getMessage().PHP_EOL;
            $error .= $e->getFile().':'.$e->getLine().PHP_EOL;
            $error .= $e->getTraceAsString();

            $runner->errorInCode(
                $code,
                new TextResult(
                    '',
                    $error,
                    $this->version,
                    \memory_get_usage(true),
                    $this->executionTime
                )
            );
        }

        return $this;
    }
}