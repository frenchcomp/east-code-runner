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
use Teknoo\East\CodeRunner\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;
use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;

/**
 * State Unregistered.
 *
 * @mixin Task
 *
 * @property StatusInterface $status
 * @property string $url
 * @property CodeInterface $code
 */
class Unregistered implements StateInterface
{
    use StateTrait;

    private function doSetCode()
    {
        /*
         * {@inheritdoc}
         */
        return function (CodeInterface $code): Task {
            $this->code = $code;

            $this->updateStates();

            return $this;
        };
    }

    private function doRegisterUrl()
    {
        /*
         * {@inheritdoc}
         */
        return function (string $taskUrl): Task {
            $this->url = $taskUrl;

            $this->updateStates();

            return $this;
        };
    }

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
}
