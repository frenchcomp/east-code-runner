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

namespace Teknoo\East\CodeRunner\Registry;

use Doctrine\ORM\EntityManager;
use Teknoo\East\CodeRunner\Entity\TaskExecution;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksByRunnerRegistryInterface;
use Teknoo\East\CodeRunner\Repository\TaskExecutionRepository;
use Teknoo\East\CodeRunner\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Service\DatesService;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;

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
     * @param EntityManager           $entityManager
     */
    public function __construct(
        DatesService $datesService,
        TaskExecutionRepository $taskExecutionRepository,
        EntityManager $entityManager
    ) {
        $this->datesService = $datesService;
        $this->taskExecutionRepository = $taskExecutionRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        if (!$offset instanceof RunnerInterface) {
            throw new \InvalidArgumentException();
        }

        $runnerIdentifier = $offset->getIdentifier();
        $taskExecution = $this->taskExecutionRepository->findByRunnerIdentifier($runnerIdentifier);

        return $taskExecution instanceof TaskExecution && !$taskExecution->getDeletedAt() instanceof \DateTime;
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
    public function offsetGet($offset)
    {
        if (!$offset instanceof RunnerInterface) {
            throw new \InvalidArgumentException();
        }

        $taskExecution = $this->getTaskExecution($offset);

        if (!$taskExecution instanceof TaskExecution) {
            return null;
        }

        return $taskExecution->getTask();
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

        return $taskExecution;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!$offset instanceof RunnerInterface) {
            throw new \InvalidArgumentException();
        }

        $taskExecution = $this->getTaskExecution($offset);

        if ($taskExecution instanceof TaskExecution) {
            $taskExecution->setTask($value);
        } else {
            $taskExecution = $this->create($value, $offset);
        }

        $this->save($taskExecution);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        if (!$offset instanceof RunnerInterface) {
            throw new \InvalidArgumentException();
        }

        $taskExecution = $this->getTaskExecution($offset);

        if ($taskExecution instanceof TaskExecution) {
            $taskExecution->setDeletedAt($this->datesService->getDate());

            $this->save($taskExecution);
        }

        $this->taskExecutionRepository->clearExecution($offset->getIdentifier());
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
