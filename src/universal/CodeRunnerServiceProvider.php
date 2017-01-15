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

namespace Teknoo\East\CodeRunner;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Interop\Container\ContainerInterface;
use Interop\Container\ServiceProvider;
use Teknoo\East\CodeRunner\Entity\Task\Task;
use Teknoo\East\CodeRunner\Entity\TaskExecution;
use Teknoo\East\CodeRunner\Entity\TaskRegistration;
use Teknoo\East\CodeRunner\Entity\TaskStandby;
use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Manager\RunnerManager\RunnerManager;
use Teknoo\East\CodeRunner\Manager\TaskManager;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksByRunnerRegistryInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksRegistryInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksStandbyRegistryInterface;
use Teknoo\East\CodeRunner\Registry\TasksByRunnerRegistry;
use Teknoo\East\CodeRunner\Registry\TasksManagerByTasksRegistry;
use Teknoo\East\CodeRunner\Registry\TasksRegistry;
use Teknoo\East\CodeRunner\Registry\TasksStandbyRegistry;
use Teknoo\East\CodeRunner\Repository\TaskExecutionRepository;
use Teknoo\East\CodeRunner\Repository\TaskRegistrationRepository;
use Teknoo\East\CodeRunner\Repository\TaskRepository;
use Teknoo\East\CodeRunner\Repository\TaskStandbyRepository;
use Teknoo\East\CodeRunner\Runner\Capability;
use Teknoo\East\CodeRunner\Runner\RemotePHP7Runner\RemotePHP7Runner;
use Teknoo\East\CodeRunner\Service\DatesService;

/**
 * Definition provider following PSR 11 Draft to build an universal bundle/package.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class CodeRunnerServiceProvider implements ServiceProvider
{
    /**
     * @param ContainerInterface $container
     *
     * @return EntityManagerInterface
     */
    private static function getEntityManager(ContainerInterface $container): EntityManagerInterface
    {
        $entityManager = null;

        if ($container->has('doctrine.orm.default_entity_manager')) {
            $entityManager = $container->get('doctrine.orm.default_entity_manager');
        }

        if ($container->has(EntityManagerInterface::class)) {
            $entityManager = $container->get(EntityManagerInterface::class);
        }

        if (!$entityManager instanceof EntityManagerInterface) {
            throw new \RuntimeException('Missing Entity Manager in the container');
        }

        return $entityManager;
    }

    /**
     * @param ContainerInterface $container
     * @param string             $repositoryBundle
     *
     * @return EntityRepository|ObjectRepository
     */
    private static function createRepository(
        ContainerInterface $container,
        string $repositoryBundle
    ): EntityRepository {
        $entityManager = static::getEntityManager($container);

        return $entityManager->getRepository($repositoryBundle);
    }

    /**
     * @param ContainerInterface $container
     *
     * @return TaskRepository
     */
    public static function createTaskRepository(ContainerInterface $container): TaskRepository
    {
        return static::createRepository($container, Task::class);
    }

    /**
     * @param ContainerInterface $container
     *
     * @return TaskExecutionRepository
     */
    public static function createTaskExecutionRepository(ContainerInterface $container): TaskExecutionRepository
    {
        return static::createRepository($container, TaskExecution::class);
    }

    /**
     * @param ContainerInterface $container
     *
     * @return TaskRegistrationRepository
     */
    public static function createTaskRegistrationRepository(ContainerInterface $container): TaskRegistrationRepository
    {
        return static::createRepository($container, TaskRegistration::class);
    }

    /**
     * @param ContainerInterface $container
     *
     * @return TaskStandbyRepository
     */
    public static function createTaskStandbyRepository(ContainerInterface $container): TaskStandbyRepository
    {
        return static::createRepository($container, TaskStandby::class);
    }

    /**
     * @return DatesService
     */
    public static function createDatesService(): DatesService
    {
        return new DatesService();
    }

    /**
     * @param ContainerInterface $container
     *
     * @return TasksByRunnerRegistryInterface
     */
    public static function createRegistryTasksByRunner(ContainerInterface $container): TasksByRunnerRegistryInterface
    {
        return new TasksByRunnerRegistry(
            $container->get(DatesService::class),
            $container->get(TaskExecutionRepository::class),
            static::getEntityManager($container)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return TasksManagerByTasksRegistryInterface
     */
    public static function createRegistryTasksMangerByTask(
        ContainerInterface $container
    ): TasksManagerByTasksRegistryInterface {
        return new TasksManagerByTasksRegistry(
            $container->get(DatesService::class),
            $container->get(TaskRegistrationRepository::class),
            static::getEntityManager($container)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return TasksStandbyRegistryInterface
     */
    public static function createRegistryTasksStandBy(
        ContainerInterface $container
    ): TasksStandbyRegistryInterface {
        return new TasksStandbyRegistry(
            $container->get(DatesService::class),
            $container->get(TaskStandbyRepository::class),
            static::getEntityManager($container)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return TasksRegistryInterface
     */
    public static function createRegistryTasks(
        ContainerInterface $container
    ): TasksRegistryInterface {
        return new TasksRegistry(
            $container->get(TaskRepository::class)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return RunnerManagerInterface
     */
    public static function createRunnerManager(ContainerInterface $container): RunnerManagerInterface
    {
        return new RunnerManager(
            $container->get(TasksByRunnerRegistryInterface::class),
            $container->get(TasksManagerByTasksRegistryInterface::class),
            $container->get(TasksStandbyRegistryInterface::class)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return TaskManagerInterface
     */
    public static function createTaskManager(ContainerInterface $container): TaskManagerInterface
    {
        $manager = new TaskManager(
            $container->get('teknoo.east.bundle.coderunner.manager.tasks.identifier'),
            $container->get('teknoo.east.bundle.coderunner.manager.tasks.url'),
            static::getEntityManager($container),
            $container->get(DatesService::class),
            $container->get(RunnerManagerInterface::class)
        );

        return $manager;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return RemotePHP7Runner
     */
    public static function createRemotePHP7Runner(ContainerInterface $container): RemotePHP7Runner
    {
        $runner = new RemotePHP7Runner(
            $container->get('teknoo.east.bundle.coderunner.vendor.old_sound_producer.remote_php7.task'),
            $container->get('teknoo.east.bundle.coderunner.runner.remote_php7.identifier'),
            $container->get('teknoo.east.bundle.coderunner.runner.remote_php7.name'),
            $container->get('teknoo.east.bundle.coderunner.runner.remote_php7.version'), [
                new Capability('platform', 'php7'),
                new Capability('feature', 'composer'),
                new Capability('feature', 'curl'),
                new Capability('feature', 'zip'),
            ]
        );

        /**
         * @var RunnerManagerInterface $runnerManager
         */
        $runnerManager = $container->get(RunnerManagerInterface::class);
        $runnerManager->registerMe($runner);

        return $runner;
    }

    /**
     * {@inheritdoc}
     */
    public function getServices()
    {
        return [
            TaskRepository::class => [static::class, 'createTaskRepository'],
            TaskExecutionRepository::class => [static::class, 'createTaskExecutionRepository'],
            TaskRegistrationRepository::class => [static::class, 'createTaskRegistrationRepository'],
            TaskStandbyRepository::class => [static::class, 'createTaskStandbyRepository'],

            DatesService::class => [static::class, 'createDatesService'],

            TasksByRunnerRegistryInterface::class => [static::class, 'createRegistryTasksByRunner'],
            TasksManagerByTasksRegistryInterface::class => [static::class, 'createRegistryTasksMangerByTask'],
            TasksStandbyRegistryInterface::class => [static::class, 'createRegistryTasksStandBy'],
            TasksRegistryInterface::class => [static::class, 'createRegistryTasks'],

            RunnerManagerInterface::class => [static::class, 'createRunnerManager'],
            TaskManagerInterface::class => [static::class, 'createTaskManager'],

            RemotePHP7Runner::class => [static::class, 'createRemotePHP7Runner'],
        ];
    }
}
