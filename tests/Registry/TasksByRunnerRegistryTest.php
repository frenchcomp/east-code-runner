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
use Teknoo\East\CodeRunnerBundle\Entity\TaskExecution;
use Teknoo\East\CodeRunnerBundle\Registry\Interfaces\TasksByRunnerRegistryInterface;
use Teknoo\East\CodeRunnerBundle\Registry\TasksByRunnerRegistry;
use Teknoo\East\CodeRunnerBundle\Repository\TaskExecutionRepository;
use Teknoo\East\CodeRunnerBundle\Service\DatesService;

/**
 * Test TasksByRunnerRegistryTest
 * @covers Teknoo\East\CodeRunnerBundle\Registry\TasksByRunnerRegistry
 */
class TasksByRunnerRegistryTest extends AbstractTasksByRunnerRegistryTest
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
     * @var array|TaskExecution[]
     */
    public $taskExecutionList = [];

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
     * @return TaskExecutionRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getTaskExecutionRepositoryMock(): TaskExecutionRepository
    {
        if (!$this->taskExecutionRepository instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->taskExecutionRepository = $this->createMock(TaskExecutionRepository::class);

            $this->taskExecutionRepository
                ->expects(self::any())
                ->method('findByRunnerIdentifier')
                ->willReturnCallback(function ($identifier) {
                    if (isset($this->taskExecutionList[$identifier])) {
                        return $this->taskExecutionList[$identifier];
                    }

                    return false;
                });
            
            $this->taskExecutionRepository
                ->expects(self::any())
                ->method('clearExecution')
                ->willReturnCallback(function ($identifier) {
                    if (isset($this->taskExecutionList[$identifier])) {
                        unset($this->taskExecutionList[$identifier]);
                    }

                    return $this->taskExecutionRepository;
                });

            $this->taskExecutionRepository
                ->expects(self::any())
                ->method('clearAll')
                ->willReturnCallback(function () {
                    $this->taskExecutionList = [];

                    return $this->taskExecutionRepository;
                });
        }

        return $this->taskExecutionRepository;
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
                ->willReturnCallback(function (TaskExecution $execution) {
                    $this->taskExecutionList[$execution->getRunnerIdentifier()] = $execution;
                });
        }

        return $this->entityManager;
    }

    /**
     * @return TasksByRunnerRegistryInterface
     */
    public function buildRegistry(): TasksByRunnerRegistryInterface
    {
        return new TasksByRunnerRegistry(
            $this->getDatesServiceMock(),
            $this->getTaskExecutionRepositoryMock(),
            $this->getEntityManagerMock()
        );
    }

}