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

namespace Teknoo\East\CodeRunner\Registry;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Teknoo\East\CodeRunner\Entity\TaskRegistration;
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunner\Repository\TaskRegistrationRepository;
use Teknoo\East\CodeRunner\Service\DatesService;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\Foundation\Promise\PromiseInterface;

/**
 * Class TasksManagerByTasksRegistry.
 * Default implementation of TasksManagerByTasksRegistryInterface to persist the task manager managing a task.
 * The registry is usable via an array access behavior, with tasks as key, to return and manipulate managers. Manager
 * are identified by their id referenced in the platform, but manager must be referenced into the registry.
 * The registry use TaskRegistration entity to persist and manage the relation.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class TasksManagerByTasksRegistry implements TasksManagerByTasksRegistryInterface
{
    /**
     * @var DatesService
     */
    private $datesService;

    /**
     * @var TaskRegistrationRepository
     */
    private $taskRegistrationRepository;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TaskManagerInterface[]
     */
    private $tasksManagersList = [];

    /**
     * TasksManagerByTasksRegistry constructor.
     *
     * @param DatesService               $datesService
     * @param TaskRegistrationRepository $repository
     * @param EntityManagerInterface     $entityManager
     */
    public function __construct(
        DatesService $datesService,
        TaskRegistrationRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        $this->datesService = $datesService;
        $this->taskRegistrationRepository = $repository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function addTaskManager(TaskManagerInterface $taskManager): TasksManagerByTasksRegistryInterface
    {
        $this->tasksManagersList[$taskManager->getIdentifier()] = $taskManager;
        $taskManager->addRegistry($this);

        return $this;
    }

    /**
     * To extract a task from a TaskRegistration entity.
     *
     * @param TaskInterface $task
     *
     * @return null|TaskRegistration
     */
    private function getTaskRegistration(TaskInterface $task)
    {
        $taskId = $task->getId();
        $taskRegistration = $this->taskRegistrationRepository->findByTaskId($taskId);

        if (!$taskRegistration instanceof TaskRegistration || $taskRegistration->getDeletedAt() instanceof \DateTime) {
            return null;
        }

        return $taskRegistration;
    }

    /**
     * {@inheritdoc}
     */
    public function get(TaskInterface $task, PromiseInterface $promise): TasksManagerByTasksRegistryInterface
    {
        $taskRegistration = $this->getTaskRegistration($task);

        if (!$taskRegistration instanceof TaskRegistration) {
            $promise->fail(new \DomainException('The task has not been registered'));

            return $this;
        }

        $taskManagerIdentifier = $taskRegistration->getTaskManagerIdentifier();

        if (!isset($this->tasksManagersList[$taskManagerIdentifier])) {
            $promise->fail(new \DomainException('The manager has not been referenced'));

            return $this;
        }

        $promise->success($this->tasksManagersList[$taskManagerIdentifier]);

        return $this;
    }

    /**
     * @param TaskRegistration $taskRegistration
     */
    private function save(TaskRegistration $taskRegistration)
    {
        $this->entityManager->persist($taskRegistration);
        $this->entityManager->flush();
    }

    /**
     * To create a new TaskRegistration instance to persist the manager owning a task.
     *
     * @param TaskInterface        $task
     * @param TaskManagerInterface $manager
     *
     * @return TaskRegistration
     */
    private function create(TaskInterface $task, TaskManagerInterface $manager): TaskRegistration
    {
        $taskExecution = new TaskRegistration();
        $taskExecution->setTask($task);
        $taskExecution->setTaskManagerIdentifier($manager->getIdentifier());

        $this->taskRegistrationRepository->clearRegistration($task->getId());

        return $taskExecution;
    }

    /**
     * {@inheritdoc}
     */
    public function register(TaskInterface $task, TaskManagerInterface $manager): TasksManagerByTasksRegistryInterface
    {
        $taskRegistration = $this->getTaskRegistration($task);

        if ($taskRegistration instanceof TaskRegistration) {
            $taskRegistration->setTaskManagerIdentifier($manager->getIdentifier());
        } else {
            $taskRegistration = $this->create($task, $manager);
        }

        $this->save($taskRegistration);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(TaskInterface $task): TasksManagerByTasksRegistryInterface
    {
        $taskRegistration = $this->getTaskRegistration($task);

        if ($taskRegistration instanceof TaskRegistration) {
            $this->taskRegistrationRepository->clearRegistration($task->getId());
            $taskRegistration->setDeletedAt($this->datesService->getDate());

            $this->save($taskRegistration);
        }

        $this->taskRegistrationRepository->clearRegistration($task->getId());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearAll(): TasksManagerByTasksRegistryInterface
    {
        $this->taskRegistrationRepository->clearAll($this->datesService->getDate());

        return $this;
    }
}
