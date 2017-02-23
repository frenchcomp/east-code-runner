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

use Teknoo\East\CodeRunner\Entity\Task\Task;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksRegistryInterface;
use Teknoo\East\CodeRunner\Repository\TaskRepository;
use Teknoo\East\Foundation\Promise\PromiseInterface;

/**
 * Class TasksRegistry.
 * Default implementation of TasksRegistryInterface to return a specific task identfied by its uuid.
 * This repository reuse Task's entity.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class TasksRegistry implements TasksRegistryInterface
{
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * TasksRegistry constructor.
     *
     * @param TaskRepository $taskRepository
     */
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $taskUid, PromiseInterface $promise): TasksRegistryInterface
    {
        $task = $this->taskRepository->findOneBy(['id' => $taskUid, 'deletedAt' => null]);

        if (!$task instanceof Task) {
            $promise->fail(new \DomainException('Error, the task was not found'));

            return $this;
        }

        $promise->success($task);

        return $this;
    }
}
