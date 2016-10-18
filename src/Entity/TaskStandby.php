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

use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;

class TaskStandby
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $runnerIdentifier;

    /**
     * @var TaskInterface
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
    public function getRunnerIdentifier(): string
    {
        return $this->runnerIdentifier;
    }

    /**
     * @param string $runnerIdentifier
     * @return TaskStandby
     */
    public function setRunnerIdentifier(string $runnerIdentifier): TaskStandby
    {
        $this->runnerIdentifier = $runnerIdentifier;

        return $this;
    }

    /**
     * @return TaskInterface
     */
    public function getTask(): TaskInterface
    {
        return $this->task;
    }

    /**
     * @param TaskInterface $task
     * @return TaskStandby
     */
    public function setTask(TaskInterface $task): TaskStandby
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
     * @return TaskStandby
     */
    public function setCreatedAt(\DateTime $createdAt): TaskStandby
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
     * @return TaskStandby
     */
    public function setUpdatedAt(\DateTime $updatedAt): TaskStandby
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
     * @param \DateTime|null $deletedAt
     *
     * @return TaskStandby
     */
    public function setDeletedAt(\DateTime $deletedAt=null): TaskStandby
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}