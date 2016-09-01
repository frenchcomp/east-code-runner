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
namespace Teknoo\East\CodeRunnerBundle\Entity\Task\States;

use Teknoo\East\CodeRunnerBundle\Entity\Task\Task;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\StatusInterface;
use Teknoo\States\State\AbstractState;

/**
 * State Registered
 * @mixin Task
 */
class Registered extends AbstractState
{
    /**
     * {@inheritdoc}
     */
    private function doRegisterStatus(StatusInterface $status): Task
    {
        $this->status = $status;

        return $this;
    }

    private function doRegisterResult(ResultInterface $result): Task
    {
        $this->result = $result;

        return $this;
    }
}