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
namespace Teknoo\East\CodeRunnerBundle\Worker\Interfaces;

use Teknoo\East\CodeRunnerBundle\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;

/**
 * Interface RunnerInterface
 * @package Teknoo\East\CodeRunnerBundle\Worker\PHP7Runner\Interfaces
 */
interface RunnerInterface
{
    /**
     * @param CodeInterface $code
     * @return RunnerInterface
     */
    public function composerIsReady(CodeInterface $code): RunnerInterface;

    /**
     * @param CodeInterface $code
     * @param ResultInterface $result
     * @return RunnerInterface
     */
    public function codeExecuted(CodeInterface $code, ResultInterface $result): RunnerInterface;

    /**
     * @param CodeInterface $code
     * @param ResultInterface $result
     * @return RunnerInterface
     */
    public function errorInCode(CodeInterface $code, ResultInterface $result): RunnerInterface;
}