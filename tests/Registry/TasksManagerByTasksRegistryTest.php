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
namespace Teknoo\Tests\East\CodeRunnerBundle\Registry;

use Doctrine\ORM\EntityManager;
use Teknoo\East\CodeRunnerBundle\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunnerBundle\Registry\TasksManagerByTasksRegistry;
use Teknoo\East\CodeRunnerBundle\Repository\TaskRegistrationRepository;
use Teknoo\East\CodeRunnerBundle\Service\DatesService;

class TasksManagerByTasksRegistryTest extends AbstractTasksManagerByTasksRegistryTest
{
    /**
     * @var DatesService
     */
    private $datesService;

    /**
     * @var TaskRegistrationRepository
     */
    private $taskRegistrationRepository;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @return DatesService
     */
    public function getDatesServiceMock(): DatesService
    {
        if (!$this->datesService instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->datesService = $this->createMock(DatesService::class);
        }

        return $this->datesService;
    }

    /**
     * @return TaskRegistrationRepository
     */
    public function getTaskRegistrationRepositoryMock(): TaskRegistrationRepository
    {
        if (!$this->taskRegistrationRepository instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->taskRegistrationRepository = $this->createMock(TaskRegistrationRepository::class);
        }

        return $this->taskRegistrationRepository;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManagerMock(): EntityManager
    {
        if (!$this->entityManager instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->entityManager = $this->createMock(EntityManager::class);
        }

        return $this->entityManager;
    }

    /**
     * @return TasksManagerByTasksRegistryInterface
     */
    public function buildRegistry(): TasksManagerByTasksRegistryInterface
    {
        return new TasksManagerByTasksRegistry(
            $this->getDatesServiceMock(),
            $this->getTaskRegistrationRepositoryMock(),
            $this->getEntityManagerMock()
        );
    }
}