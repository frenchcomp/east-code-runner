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

namespace Teknoo\East\CodeRunner\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Teknoo\East\CodeRunner\Entity\Task\Task;
use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunner\Service\DatesService;
use Teknoo\East\CodeRunner\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\CodeRunner\Task\Status;

/**
 * Class TaskManager.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class TaskManager implements TaskManagerInterface
{
    /**
     * @var string
     */
    private $managerIdentifier;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var string
     */
    private $urlTaskPattern;

    /**
     * @var DatesService
     */
    private $datesService;

    /**
     * @var RunnerManagerInterface
     */
    private $runnerManager;

    /**
     * @var TasksManagerByTasksRegistryInterface[]
     */
    private $registries = [];

    /**
     * Manager constructor.
     * Initialize States behavior.
     *
     * @param string                 $managerIdentifier
     * @param string                 $urlTaskPattern
     * @param EntityManagerInterface $entityManager
     * @param DatesService           $datesService
     */
    public function __construct(
        string $managerIdentifier,
        string $urlTaskPattern,
        EntityManagerInterface $entityManager,
        DatesService $datesService
    ) {
        if (empty($managerIdentifier)) {
            throw new \RuntimeException('Error, all task manager need a string identifier');
        }

        if (empty($urlTaskPattern)) {
            throw new \RuntimeException("Error, the task url pattern is missing for %$managerIdentifier%");
        }

        $this->managerIdentifier = $managerIdentifier;
        $this->entityManager = $entityManager;
        $this->urlTaskPattern = $urlTaskPattern;
        $this->datesService = $datesService;
    }

    /**
     * {@inheritdoc}
     */
    public function registerRunnerManager(RunnerManagerInterface $runnerManager): TaskManagerInterface
    {
        $this->runnerManager = $runnerManager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addRegistry(TasksManagerByTasksRegistryInterface $registry): TaskManagerInterface
    {
        $this->registries[] = $registry;

        return $this;
    }

    /**
     * @param TaskInterface $task
     *
     * @return TaskManager
     */
    private function persistTask(TaskInterface $task): TaskManager
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return $this->managerIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function executeMe(TaskInterface $task): TaskManagerInterface
    {
        $this->doRegisterAndExecuteTask($task);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function taskStatusIsUpdated(TaskInterface $task, StatusInterface $status): TaskManagerInterface
    {
        $task->registerStatus($status);
        $this->persistTask($task);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function taskResultIsUpdated(TaskInterface $task, ResultInterface $result): TaskManagerInterface
    {
        $task->registerResult($this, $result);
        $this->persistTask($task);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function forgetMe(TaskInterface $task): TaskManagerInterface
    {
        if ($task instanceof Task) {
            $this->removeTask($task);
        }

        return $this;
    }

    /**
     * @param TaskInterface $task
     *
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
     *
     * @return TaskManager
     */
    private function dispatchToRunnerManager(TaskInterface $task): TaskManager
    {
        if ($this->runnerManager instanceof RunnerManagerInterface) {
            $this->runnerManager->executeForMeThisTask($this, $task);
            $this->persistTask($task);
        } else {
            throw new \RuntimeException('Error, no Runner manager referenced');
        }

        return $this;
    }

    /**
     * @param TaskInterface $task
     *
     * @return TaskManager
     */
    private function doRegisterAndExecuteTask(TaskInterface $task): TaskManager
    {
        $task->registerStatus(new Status(Status::STATUS_REGISTERED));
        $this->persistTask($task);
        $this->generateUrl($task);

        foreach ($this->registries as $registry) {
            $registry[$task] = $this;
        }

        $this->dispatchToRunnerManager($task);

        return $this;
    }

    /**
     * @param Task $task
     *
     * @return TaskManager
     */
    private function removeTask(Task $task): TaskManager
    {
        $task->setDeletedAt($this->datesService->getDate());
        $this->persistTask($task);

        return $this;
    }
}
