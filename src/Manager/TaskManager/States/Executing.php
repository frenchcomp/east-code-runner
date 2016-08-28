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
namespace Teknoo\East\CodeRunnerBundle\Manager\TaskManager\States;

use Doctrine\ORM\EntityManager;
use Teknoo\East\CodeRunnerBundle\Manager\TaskManager\TaskManager;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\States\State\AbstractState;

/**
 * State Executing
 * @property string $urlTaskPattern
 * @property EntityManager $entityManager
 * @mixin TaskManager
 */
class Executing extends AbstractState
{
    /**
     * @param TaskInterface $task
     * @return TaskManager
     */
    private function generateUrl(TaskInterface $task): TaskManager
    {
        $url = \str_replace('UUID', $task->getId(), $this->urlTaskPattern);
        $task->registerUrl($url);

        return $this;
    }

    /**
     * @param TaskInterface $task
     * @return TaskManager
     */
    private function persistTask(TaskInterface $task): TaskManager
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $this;
    }

    /**
     * @param TaskInterface $task
     * @return TaskManager
     */
    private function doRegisterAndExecuteTask(TaskInterface $task): TaskManager
    {
        $this->persistTask($task);
        $this->generateUrl($task);
        $this->entityManager->flush();

        $this->switchState(Executing::class);

        return $this;
    }
}