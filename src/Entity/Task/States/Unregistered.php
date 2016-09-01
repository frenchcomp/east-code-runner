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
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\StatusInterface;
use Teknoo\States\State\AbstractState;

/**
 * State Unregistered
 * @mixin Task
 * @property StatusInterface $status
 * @property string $url
 * @property CodeInterface $code
 */
class Unregistered extends AbstractState
{
    /**
     * {@inheritdoc}
     */
    private function doSetCode(CodeInterface $code): Task
    {
        $this->code = $code;

        $this->updateStates();

        return $this;
    }

        /**
     * {@inheritdoc}
     */
    private function doRegisterUrl(string $taskUrl): Task
    {
        $this->url = $taskUrl;

        $this->updateStates();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    private function doRegisterStatus(StatusInterface $status): Task
    {
        $this->status = $status;

        return $this;
    }
}