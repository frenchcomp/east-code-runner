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
use Teknoo\East\CodeRunner\Service\DatesService;

/**
 * Tests TaskManagerTest.
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
     * @param string $managerIdentifier
     * @param string $urlTaskPattern
     *
     * @return TaskManagerInterface
     */
    public function buildManager(
        string $managerIdentifier = 'managerId',
        string $urlTaskPattern = 'https://foo.bar/task/UUID'
    ): TaskManagerInterface {
        return new TaskManager(
            $managerIdentifier,
            $urlTaskPattern,
            $this->getEntityManagerMock(),
            $this->getDatesServiceMock(),
            $this->getRunnerManagerMock()
        );
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
}
