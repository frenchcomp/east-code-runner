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
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east/coderunner Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\Tests\East\CodeRunner\Registry;

use Doctrine\ORM\EntityManager;
use Teknoo\East\CodeRunner\Entity\TaskStandby;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksStandbyRegistryInterface;
use Teknoo\East\CodeRunner\Registry\TasksStandbyRegistry;
use Teknoo\East\CodeRunner\Repository\TaskStandbyRepository;
use Teknoo\East\CodeRunner\Service\DatesService;

class TasksStandbyRegistryTest extends AbstractTasksStandbyRegistryTest
{
    /**
     * @var DatesService
     */
    private $datesService;

    /**
     * @var TaskStandbyRepository
     */
    private $taskStandbyRepository;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var array|TaskStandby[]
     */
    public $taskStandbyList = [];

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
     * @return TaskStandbyRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getTaskStandbyRepositoryMock(): TaskStandbyRepository
    {
        if (!$this->taskStandbyRepository instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->taskStandbyRepository = $this->createMock(TaskStandbyRepository::class);

            $this->taskStandbyRepository
                ->expects(self::any())
                ->method('fetchNextTaskStandby')
                ->willReturnCallback(function ($identifier) {
                    if (isset($this->taskStandbyList[$identifier])) {
                        return array_shift($this->taskStandbyList[$identifier]);
                    }

                    return null;
                });

            $this->taskStandbyRepository
                ->expects(self::any())
                ->method('clearAll')
                ->willReturnCallback(function () {
                    $this->taskStandbyList = [];

                    return $this->taskStandbyRepository;
                });
        }

        return $this->taskStandbyRepository;
    }

    /**
     * @return EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getEntityManagerMock(): EntityManager
    {
        if (!$this->entityManager instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->entityManager = $this->createMock(EntityManager::class);

            $this->entityManager
                ->expects(self::any())
                ->method('persist')
                ->willReturnCallback(function (TaskStandby $execution) {
                    $this->taskStandbyList[$execution->getRunnerIdentifier()][] = $execution;
                });
        }

        return $this->entityManager;
    }
    
    public function buildRegistry(): TasksStandbyRegistryInterface
    {
        return new TasksStandbyRegistry(
            $this->getDatesServiceMock(),
            $this->getTaskStandbyRepositoryMock(),
            $this->getEntityManagerMock()
        );
    }
}