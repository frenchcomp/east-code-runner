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

namespace Teknoo\Tests\East\CodeRunner;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Interop\Container\ContainerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Teknoo\East\CodeRunner\CodeRunnerServiceProvider;
use Teknoo\East\CodeRunner\Entity\Task\Task;
use Teknoo\East\CodeRunner\Entity\TaskExecution;
use Teknoo\East\CodeRunner\Entity\TaskRegistration;
use Teknoo\East\CodeRunner\Entity\TaskStandby;
use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Manager\TaskManager;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksByRunnerRegistryInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksStandbyRegistryInterface;
use Teknoo\East\CodeRunner\Repository\TaskExecutionRepository;
use Teknoo\East\CodeRunner\Repository\TaskRegistrationRepository;
use Teknoo\East\CodeRunner\Repository\TaskRepository;
use Teknoo\East\CodeRunner\Repository\TaskStandbyRepository;
use Teknoo\East\CodeRunner\Runner\RemotePHP7Runner\RemotePHP7Runner;
use Teknoo\East\CodeRunner\Service\DatesService;

/**
 * Class CodeRunnerServiceProviderTest
 * @covers \Teknoo\East\CodeRunner\CodeRunnerServiceProvider
 */
class CodeRunnerServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return CodeRunnerServiceProvider
     */
    public function buildProvider()
    {
        return new CodeRunnerServiceProvider();
    }

    /**
     * @param ContainerInterface|\PHPUnit_Framework_MockObject_MockObject $containerMock
     * @param string $repositoryName
     * @param EntityRepository|ObjectRepository|\PHPUnit_Framework_MockObject_MockObject $repositoryMock
     */
    private function prepareRepository($containerMock, $repositoryName, $repositoryMock)
    {
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $containerMock->expects(self::any())
            ->method('has')
            ->willReturn(true);

        $containerMock->expects(self::any())
            ->method('get')
            ->willReturn($entityManagerMock);

        $entityManagerMock->expects(self::any())
            ->method('getRepository')
            ->with($repositoryName)
            ->willReturn($repositoryMock);
    }

    public function testCreateTaskRepository()
    {
        $container = $this->createMock(ContainerInterface::class);
        $this->prepareRepository($container, Task::class, $this->createMock(TaskRepository::class));

        self::assertInstanceOf(
            TaskRepository::class,
            $this->buildProvider()->createTaskRepository($container)
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateTaskRepositoryBadManager()
    {
        $container = $this->createMock(ContainerInterface::class);
        $this->buildProvider()->createTaskRepository($container);
    }

    public function testCreateTaskExecutionRepository()
    {
        $container = $this->createMock(ContainerInterface::class);
        $this->prepareRepository($container, TaskExecution::class, $this->createMock(TaskExecutionRepository::class));

        self::assertInstanceOf(
            TaskExecutionRepository::class,
            $this->buildProvider()->createTaskExecutionRepository($container)
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateTaskExecutionRepositoryBadManager()
    {
        $container = $this->createMock(ContainerInterface::class);
        $this->buildProvider()->createTaskExecutionRepository($container);
    }

    public function testCreateTaskRegistrationRepository()
    {
        $container = $this->createMock(ContainerInterface::class);
        $this->prepareRepository($container, TaskRegistration::class, $this->createMock(TaskRegistrationRepository::class));

        self::assertInstanceOf(
            TaskRegistrationRepository::class,
            $this->buildProvider()->createTaskRegistrationRepository($container)
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateTaskRegistrationRepositoryBadManager()
    {
        $container = $this->createMock(ContainerInterface::class);
        $this->buildProvider()->createTaskRegistrationRepository($container);
    }

    public function testCreateTaskStandbyRepository()
    {
        $container = $this->createMock(ContainerInterface::class);
        $this->prepareRepository($container, TaskStandby::class, $this->createMock(TaskStandbyRepository::class));

        self::assertInstanceOf(
            TaskStandbyRepository::class,
            $this->buildProvider()->createTaskStandbyRepository($container)
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateTaskStandbyRepositoryBadManager()
    {
        $container = $this->createMock(ContainerInterface::class);
        $this->buildProvider()->createTaskStandbyRepository($container);
    }


    public function testCreateDatesService()
    {
        self::assertInstanceOf(
            DatesService::class,
            $this->buildProvider()->createDatesService()
        );
    }

    public function testCreateRegistryTasksByRunner()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects(self::any())
            ->method('has')
            ->willReturn(true);

        $container->expects(self::any())
            ->method('get')
            ->willReturnCallback(function ($name) {
                switch ($name) {
                    case 'doctrine.orm.default_entity_manager':
                    case EntityManagerInterface::class:
                        return $this->createMock(EntityManagerInterface::class);
                        break;
                    case DatesService::class:
                        return $this->createMock(DatesService::class);
                        break;
                    case TaskExecutionRepository::class:
                        return $this->createMock(TaskExecutionRepository::class);
                        break;
                }
            });

        self::assertInstanceOf(
            TasksByRunnerRegistryInterface::class,
            $this->buildProvider()->createRegistryTasksByRunner($container)
        );
    }

    public function testCreateRegistryTasksMangerByTask()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects(self::any())
            ->method('has')
            ->willReturn(true);

        $container->expects(self::any())
            ->method('get')
            ->willReturnCallback(function ($name) {
                switch ($name) {
                    case 'doctrine.orm.default_entity_manager':
                    case EntityManagerInterface::class:
                        return $this->createMock(EntityManagerInterface::class);
                        break;
                    case DatesService::class:
                        return $this->createMock(DatesService::class);
                        break;
                    case TaskRegistrationRepository::class:
                        return $this->createMock(TaskRegistrationRepository::class);
                        break;
                }
            });

        self::assertInstanceOf(
            TasksManagerByTasksRegistryInterface::class,
            $this->buildProvider()->createRegistryTasksMangerByTask($container)
        );
    }

    public function testCreateRegistryTasksStandBy()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects(self::any())
            ->method('has')
            ->willReturn(true);

        $container->expects(self::any())
            ->method('get')
            ->willReturnCallback(function ($name) {
                switch ($name) {
                    case 'doctrine.orm.default_entity_manager':
                    case EntityManagerInterface::class:
                        return $this->createMock(EntityManagerInterface::class);
                        break;
                    case DatesService::class:
                        return $this->createMock(DatesService::class);
                        break;
                    case TaskStandbyRepository::class:
                        return $this->createMock(TaskStandbyRepository::class);
                        break;
                }
            });

        self::assertInstanceOf(
            TasksStandbyRegistryInterface::class,
            $this->buildProvider()->createRegistryTasksStandBy($container)
        );
    }

    public function testCreateRunnerManager()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects(self::any())
            ->method('get')
            ->willReturnCallback(function ($name) {
                switch ($name) {
                    case TasksByRunnerRegistryInterface::class:
                        return $this->createMock(TasksByRunnerRegistryInterface::class);
                        break;
                    case TasksManagerByTasksRegistryInterface::class:
                        return $this->createMock(TasksManagerByTasksRegistryInterface::class);
                        break;
                    case TasksStandbyRegistryInterface::class:
                        return $this->createMock(TasksStandbyRegistryInterface::class);
                        break;
                }
            });

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $this->buildProvider()->createRunnerManager($container)
        );
    }

    public function testCreateTaskManager()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects(self::any())
            ->method('has')
            ->willReturn(true);

        $container->expects(self::any())
            ->method('get')
            ->willReturnCallback(function ($name) {
                switch ($name) {
                    case 'teknoo.east.bundle.coderunner.manager.tasks.identifier':
                        return 'foo';
                        break;
                    case 'teknoo.east.bundle.coderunner.manager.tasks.url':
                        return 'bar';
                        break;
                    case 'doctrine.orm.default_entity_manager':
                    case EntityManagerInterface::class:
                        return $this->createMock(EntityManagerInterface::class);
                        break;
                    case DatesService::class:
                        return $this->createMock(DatesService::class);
                        break;
                    case RunnerManagerInterface::class:
                        return $this->createMock(RunnerManagerInterface::class);
                        break;
                }
            });

        self::assertInstanceOf(
            TaskManager::class,
            $this->buildProvider()->createTaskManager($container)
        );
    }

    public function testCreateRemotePHP7Runner()
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects(self::any())
            ->method('get')
            ->willReturnCallback(function ($name) {
                switch ($name) {
                    case 'teknoo.east.bundle.coderunner.runner.remote_php7.identifier':
                        return 'foo';
                        break;
                    case 'teknoo.east.bundle.coderunner.runner.remote_php7.name':
                        return 'foo';
                        break;
                    case 'teknoo.east.bundle.coderunner.runner.remote_php7.version':
                        return 'foo';
                        break;
                    case ProducerInterface::class:
                        return $this->createMock(ProducerInterface::class);
                        break;
                }
            });

        self::assertInstanceOf(
            RemotePHP7Runner::class,
            $this->buildProvider()->createRemotePHP7Runner($container)
        );
    }

    public function testGetDefinitions()
    {
        $definitions = $this->buildProvider()->getServices();
        self::assertTrue(isset($definitions[TaskRepository::class]));
        self::assertTrue(isset($definitions[TaskExecutionRepository::class]));
        self::assertTrue(isset($definitions[TaskRegistrationRepository::class]));
        self::assertTrue(isset($definitions[TaskStandbyRepository::class]));
        self::assertTrue(isset($definitions[DatesService::class]));
        self::assertTrue(isset($definitions[TasksByRunnerRegistryInterface::class]));
        self::assertTrue(isset($definitions[TasksManagerByTasksRegistryInterface::class]));
        self::assertTrue(isset($definitions[TasksStandbyRegistryInterface::class]));
        self::assertTrue(isset($definitions[RunnerManagerInterface::class]));
        self::assertTrue(isset($definitions[TaskManagerInterface::class]));
        self::assertTrue(isset($definitions[RemotePHP7Runner::class]));
    }
}