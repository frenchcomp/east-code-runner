<?php

/**
 * East CodeRunner.
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

namespace Teknoo\Tests\East\CodeRunner\Registry;

use Teknoo\East\CodeRunner\Entity\Task\Task;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksRegistryInterface;
use Teknoo\East\CodeRunner\Registry\TasksRegistry;
use Teknoo\East\CodeRunner\Repository\TaskRepository;

/**
 * @covers \Teknoo\East\CodeRunner\Registry\TasksRegistry
 */
class TasksRegistryTest extends AbstractTasksRegistryTest
{
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * @return TaskRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getTaskRepository(): TaskRepository
    {
        if (!$this->taskRepository instanceof TaskRepository) {
            $this->taskRepository = $this->createMock(TaskRepository::class);
        }

        return $this->taskRepository;
    }

    public function buildRegistry(): TasksRegistryInterface
    {
        $this->getTaskRepository()
            ->expects(self::any())
            ->method('findOneBy')
            ->willReturnCallback(function ($name) {
                if (['id'=>'fooBar'] == $name) {
                    return $this->createMock(Task::class);
                }

                return null;
            });

        return new TasksRegistry($this->getTaskRepository());
    }
}