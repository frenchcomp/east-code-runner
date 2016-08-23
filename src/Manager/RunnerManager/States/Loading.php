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
namespace Teknoo\East\CodeRunnerBundle\Manager\RunnerManager\States;

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\States\State\AbstractState;

class Loading extends AbstractState
{
    /**
     * {@inheritdoc}
     */
    private function doRegisterMe(RunnerInterface $runner): RunnerManagerInterface
    {
        $this->runners[$runner->getIdentifier()] = $runner;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    private function doForgetMe(RunnerInterface $runner): RunnerManagerInterface
    {
        $runnerIdentifier = $runner->getIdentifier();
        if (isset($this->runners[$runnerIdentifier])) {
            unset($this->runners[$runnerIdentifier]);
        }

        return $this;
    }
}