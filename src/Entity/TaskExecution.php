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
namespace Teknoo\East\CodeRunnerBundle\Entity;

use Teknoo\East\CodeRunnerBundle\Entity\Task\Task;

class TaskExecution
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $taskManagerIdentifier;

    /**
     * @var Task
     */
    private $task;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \DateTime
     */
    private $deletedAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTaskManagerIdentifier(): string
    {
        return $this->taskManagerIdentifier;
    }

    /**
     * @param string $taskManagerIdentifier
     * @return TaskExecution
     */
    public function setTaskManagerIdentifier(string $taskManagerIdentifier): TaskExecution
    {
        $this->taskManagerIdentifier = $taskManagerIdentifier;

        return $this;
    }

    /**
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @param Task $task
     * @return TaskExecution
     */
    public function setTask(Task $task): TaskExecution
    {
        $this->task = $task;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return TaskExecution
     */
    public function setCreatedAt(\DateTime $createdAt): TaskExecution
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return TaskExecution
     */
    public function setUpdatedAt(\DateTime $updatedAt): TaskExecution
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param mixed $deletedAt
     *
     * @return TaskExecution
     */
    public function setDeletedAt($deletedAt): TaskExecution
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}