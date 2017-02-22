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
use Teknoo\East\CodeRunner\Entity\TaskExecution;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksByRunnerRegistryInterface;
use Teknoo\East\CodeRunner\Repository\TaskExecutionRepository;
use Teknoo\East\CodeRunner\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Service\DatesService;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\Foundation\Promise\PromiseInterface;

/**
 * Class TasksByRunnerRegistry.
 * Default implementation of TasksByRunnerRegistryInterface to persist the task currently executed by a runner.
 * The registry use TaskExecution entity to persist and manage the relation.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class TasksByRunnerRegistry implements TasksByRunnerRegistryInterface
{
    /**
     * @var DatesService
     */
    private $datesService;

    /**
     * @var TaskExecutionRepository
     */
    private $taskExecutionRepository;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * TasksByRunnerRegistry constructor.
     *
     * @param DatesService            $datesService
     * @param TaskExecutionRepository $taskExecutionRepository
     * @param EntityManagerInterface  $entityManager
     */
    public function __construct(
        DatesService $datesService,
        TaskExecutionRepository $taskExecutionRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->datesService = $datesService;
        $this->taskExecutionRepository = $taskExecutionRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param RunnerInterface $runner
     *
     * @return null|TaskExecution
     */
    private function getTaskExecution(RunnerInterface $runner)
    {
        $runnerIdentifier = $runner->getIdentifier();
        $taskExecution = $this->taskExecutionRepository->findByRunnerIdentifier($runnerIdentifier);

        if (!$taskExecution instanceof TaskExecution || $taskExecution->getDeletedAt() instanceof \DateTime) {
            return null;
        }

        return $taskExecution;
    }

    /**
     * {@inheritdoc}
     */
    public function get(RunnerInterface $runner, PromiseInterface $promise): TasksByRunnerRegistryInterface
    {
        $taskExecution = $this->getTaskExecution($runner);

        if (!$taskExecution instanceof TaskExecution) {
            $promise->fail(new \DomainException('Runner not currently active'));

            return $this;
        }

        $promise->success($taskExecution->getTask());

        return $this;
    }

    /**
     * @param TaskExecution $taskExecution
     */
    private function save(TaskExecution $taskExecution)
    {
        $this->entityManager->persist($taskExecution);
        $this->entityManager->flush();
    }

    /**
     * To create a new TaskExecution instance to persist the runner executing a task.
     *
     * @param TaskInterface   $task
     * @param RunnerInterface $runner
     *
     * @return TaskExecution
     */
    private function create(TaskInterface $task, RunnerInterface $runner): TaskExecution
    {
        $taskExecution = new TaskExecution();
        $taskExecution->setTask($task);
        $taskExecution->setRunnerIdentifier($runner->getIdentifier());

        $this->taskExecutionRepository->clearExecution($runner->getIdentifier());

        return $taskExecution;
    }

    /**
     * {@inheritdoc}
     */
    public function register(RunnerInterface $runner, TaskInterface $task): TasksByRunnerRegistryInterface
    {
        $taskExecution = $this->getTaskExecution($runner);

        if ($taskExecution instanceof TaskExecution) {
            $taskExecution->setTask($task);
        } else {
            $taskExecution = $this->create($task, $runner);
        }

        $this->save($taskExecution);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(RunnerInterface $runner): TasksByRunnerRegistryInterface
    {
        $taskExecution = $this->getTaskExecution($runner);

        if ($taskExecution instanceof TaskExecution) {
            $taskExecution->setDeletedAt($this->datesService->getDate());

            $this->save($taskExecution);
        }

        $this->taskExecutionRepository->clearExecution($runner->getIdentifier());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearAll(): TasksByRunnerRegistryInterface
    {
        $this->taskExecutionRepository->clearAll($this->datesService->getDate());

        return $this;
    }
}
