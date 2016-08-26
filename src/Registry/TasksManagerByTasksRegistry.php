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
namespace Teknoo\East\CodeRunnerBundle\Registry;

use Doctrine\ORM\EntityManager;
use Teknoo\East\CodeRunnerBundle\Entity\TaskRegistration;
use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunnerBundle\Registry\Interfaces\TasksByRunnerRegistryInterface;
use Teknoo\East\CodeRunnerBundle\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunnerBundle\Repository\TaskRegistrationRepository;
use Teknoo\East\CodeRunnerBundle\Service\DatesService;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;

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
     * @param DatesService $datesService
     * @param TaskRegistrationRepository $taskRegistrationRepository
     * @param EntityManager $entityManager
     */
    public function __construct(
        DatesService $datesService,
        TaskRegistrationRepository $taskRegistrationRepository,
        EntityManager $entityManager
    ) {
        $this->datesService = $datesService;
        $this->taskRegistrationRepository = $taskRegistrationRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param TaskManagerInterface $taskManager
     */
    public function addTaskManager(TaskManagerInterface $taskManager)
    {
        $this->tasksManagersList[$taskManager->getIdentifier()] = $taskManager;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        if (!$offset instanceof TaskInterface) {
            throw new \InvalidArgumentException();
        }

        $url = $offset->getUrl();
        $taskRegistration = $this->taskRegistrationRepository->findByTaskUrl($url);

        return $taskRegistration instanceof TaskRegistration && !$taskRegistration->getDeletedAt() instanceof \DateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if (!$offset instanceof TaskInterface) {
            throw new \InvalidArgumentException();
        }

        $url = $offset->getUrl();
        $taskRegistration = $this->taskRegistrationRepository->findByTaskUrl($url);

        if (!$taskRegistration instanceof TaskRegistration || $taskRegistration->getDeletedAt() instanceof \DateTime) {
            return null;
        }

        $taskManagerIdentifier = $taskRegistration->getTaskManagerIdentifier();

        if (!isset($this->tasksManagersList[$taskManagerIdentifier])) {
            throw new \DomainException();
        }

        return $this->tasksManagersList[$taskManagerIdentifier];
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
     * @param TaskInterface $task
     * @param TaskManagerInterface $manager
     * @return TaskRegistration
     */
    private function create(TaskInterface $task, TaskManagerInterface $manager): TaskRegistration
    {
        $taskExecution = new TaskRegistration();
        $taskExecution->setTask($task);
        $taskExecution->setTaskManagerIdentifier($manager->getIdentifier());

        return $taskExecution;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!$offset instanceof TaskInterface) {
            throw new \InvalidArgumentException();
        }

        $taskRegistration = $this[$offset];

        if ($taskRegistration instanceof TaskRegistration) {
            $taskRegistration->setTask($value);
        } else {
            $taskRegistration = $this->create($value, $offset);
        }

        $this->save($taskRegistration);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        if (!$offset instanceof TaskInterface) {
            throw new \InvalidArgumentException();
        }

        $taskRegistration = $this[$offset];

        if ($taskRegistration instanceof TaskRegistration) {
            $taskRegistration->setDeletedAt($this->datesService->getDate());

            $this->save($taskRegistration);
        }

        $this->taskRegistrationRepository->clear($offset->getIdentifier());
    }

    /**
     * {@inheritdoc}
     */
    public function clearAll(): TasksByRunnerRegistryInterface
    {
        $this->taskRegistrationRepository->clearAll($this->datesService->getDate());
    }
}