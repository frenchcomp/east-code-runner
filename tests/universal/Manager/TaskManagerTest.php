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

namespace Teknoo\Tests\East\CodeRunner\Manager;

use Doctrine\ORM\EntityManager;
use Teknoo\East\CodeRunner\Entity\Task\Task;
use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Manager\TaskManager;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunner\Service\DatesService;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;

/**
 * Tests TaskManagerTest.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\East\CodeRunner\Manager\TaskManager
 */
class TaskManagerTest extends AbstractTaskManagerTest
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var DatesService
     */
    private $datesService;

    /**
     * @var RunnerManagerInterface
     */
    private $runnerManager;

    /**
     * @var TasksManagerByTasksRegistryInterface
     */
    private $tasksManagerByTasksRegistry;

    /**
     * @return EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getEntityManagerMock(): EntityManager
    {
        if (!$this->entityManager instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->entityManager = $this->createMock(EntityManager::class);
        }

        return $this->entityManager;
    }

    /**
     * @return DatesService|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getDatesServiceMock(): DatesService
    {
        if (!$this->datesService instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->datesService = $this->createMock(DatesService::class);
        }

        return $this->datesService;
    }

    /**
     * @return RunnerManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getRunnerManagerMock(): RunnerManagerInterface
    {
        if (!$this->runnerManager instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->runnerManager = $this->createMock(RunnerManagerInterface::class);
        }

        return $this->runnerManager;
    }

    /**
     * @return TasksManagerByTasksRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getTasksManagerByTasksRegistry(): TasksManagerByTasksRegistryInterface
    {
        if (!$this->tasksManagerByTasksRegistry instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->tasksManagerByTasksRegistry = $this->createMock(TasksManagerByTasksRegistryInterface::class);
        }

        return $this->tasksManagerByTasksRegistry;
    }

    /**
     * @param string $managerIdentifier
     * @param string $urlTaskPattern
     *
     * @return TaskManagerInterface
     */
    public function buildManager(
        string $managerIdentifier = 'managerId',
        string $urlTaskPattern = 'https://foo.bar/task/UUID'
    ): TaskManagerInterface {
        return (new TaskManager(
            $managerIdentifier,
            $urlTaskPattern,
            $this->getEntityManagerMock(),
            $this->getDatesServiceMock()
        ))->registerRunnerManager($this->getRunnerManagerMock())
            ->addRegistry($this->getTasksManagerByTasksRegistry());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testManagerNoIdentifier()
    {
        new TaskManager(
            '',
            'http://foo.bar',
            $this->getEntityManagerMock(),
            $this->getDatesServiceMock(),
            $this->getRunnerManagerMock()
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testManagerNoUrl()
    {
        new TaskManager(
            'fooBar',
            '',
            $this->getEntityManagerMock(),
            $this->getDatesServiceMock(),
            $this->getRunnerManagerMock()
        );
    }

    public function testForgetMeTaskEntity()
    {
        $this->getEntityManagerMock()
            ->expects(self::once())
            ->method('flush');

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $this->buildManager()->forgetMe($this->createMock(Task::class))
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExecuteMeNoRunner()
    {
        $manager = new TaskManager(
            'managerId',
            'https://foo.bar/task/UUID',
            $this->getEntityManagerMock(),
            $this->getDatesServiceMock()
        );
        $manager->addRegistry($this->getTasksManagerByTasksRegistry());
        $task = $this->createMock(TaskInterface::class);

        $task->expects(self::once())
            ->method('registerUrl')
            ->with(new \PHPUnit_Framework_Constraint_Not(self::isEmpty()))
            ->willReturnSelf();

        self::assertInstanceOf(
            TaskManagerInterface::class,
            $manager->executeMe($task)
        );
    }
}
