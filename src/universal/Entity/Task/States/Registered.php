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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east/coderunner Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\East\CodeRunner\Entity\Task\States;

use Teknoo\East\CodeRunner\Entity\Task\Task;
use Teknoo\East\CodeRunner\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;
use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;

/**
 * State Registered.
 *
 * @mixin Task
 *
 * @property StatusInterface $status
 * @property ResultInterface $result
 */
class Registered implements StateInterface
{
    use StateTrait;

    private function doRegisterStatus()
    {
        /*
         * {@inheritdoc}
         */
        return function (StatusInterface $status): Task {
            $this->status = $status;

            return $this;
        };
    }

    private function doRegisterResult()
    {
        /*
         * {@inheritdoc}
         */
        return function (ResultInterface $result): Task {
            $this->result = $result;

            $this->updateStates();

            return $this;
        };
    }
}
