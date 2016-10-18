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
use Teknoo\East\CodeRunnerBundle\Entity\Task\Task;
use Teknoo\East\CodeRunnerBundle\Entity\TaskStandby;
use Teknoo\East\CodeRunnerBundle\Registry\Interfaces\TasksStandbyRegistryInterface;
use Teknoo\East\CodeRunnerBundle\Repository\TaskStandbyRepository;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Service\DatesService;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;

class TasksStandbyRegistry implements TasksStandbyRegistryInterface
{
    /**
     * @var DatesService
     */
    private $datesService;

    /**
     * @var TaskStandbyRepository
     */
    private $taskStandbyRepository;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * TasksByRunnerRegistry constructor.
     * @param DatesService $datesService
     * @param TaskStandbyRepository $taskStandbyRepository
     * @param EntityManager $entityManager
     */
    public function __construct(
        DatesService $datesService,
        TaskStandbyRepository $taskStandbyRepository,
        EntityManager $entityManager
    ) {
        $this->datesService = $datesService;
        $this->taskStandbyRepository = $taskStandbyRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @param RunnerInterface $runner
     * @return null|TaskStandby
     */
    private function getNextTaskStandBy(RunnerInterface $runner)
    {
        $runnerIdentifier = $runner->getIdentifier();
        $taskStandby = $this->taskStandbyRepository->fetchNextTaskStandby($runnerIdentifier);

        if (!$taskStandby instanceof TaskStandby || $taskStandby->getDeletedAt() instanceof \DateTime) {
            return null;
        }

        return $taskStandby;
    }

    /**
     * @param TaskStandby $taskStandby
     */
    private function save(TaskStandby $taskStandby)
    {
        $this->entityManager->persist($taskStandby);
        $this->entityManager->flush();
    }

    /**
     * @param TaskInterface $task
     * @param RunnerInterface $runner
     * @return TaskStandby
     */
    private function create(TaskInterface $task, RunnerInterface $runner): TaskStandby
    {
        $taskStandby = new TaskStandby();
        $taskStandby->setTask($task);
        $taskStandby->setRunnerIdentifier($runner->getIdentifier());

        return $taskStandby;
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue(RunnerInterface $runner, Task $task): TasksStandbyRegistryInterface
    {
        $taskStandby = $this->create($task, $runner);
        $this->save($taskStandby);

        return $this;
    }

    /**
     * @param RunnerInterface $runner
     * @return null|TaskStandby
     */
    public function dequeue(RunnerInterface $runner)
    {
        return $this->getNextTaskStandBy($runner);
    }

    /**
     * {@inheritdoc}
     */
    public function clearAll(): TasksStandbyRegistryInterface
    {
        $this->taskStandbyRepository->clearAll($this->datesService->getDate());

        return $this;
    }
}